#!/usr/bin/env python3
"""
install.py — dARK Dashboard Web Installer
==========================================
Automates the full setup of the dARK Dashboard application.

Steps performed:
  1. Check prerequisites (Docker, docker-compose)
  2. Create .env from .env.example (if missing), sync DB credentials with docker-compose.yml
  3. Start Docker containers (docker-compose up -d)
  4. Wait for MySQL to be ready
  5. Install NPM dependencies (leaflet)
  6. Install PHP dependencies (composer install) inside the container
  7. Generate Laravel APP_KEY
  8. Run database migrations and seeders
  9. Create storage symbolic link
 10. Display success message with access URL and default credentials

Usage:
  python3 install.py          # Full installation
  python3 install.py --reset  # Destroy containers/volumes and reinstall from scratch
"""

import subprocess
import sys
import os
import time
import shutil
import argparse
import re

# ─── Configuration ──────────────────────────────────────────────────────────────

PROJECT_DIR = os.path.dirname(os.path.abspath(__file__))
ENV_FILE = os.path.join(PROJECT_DIR, ".env")
ENV_EXAMPLE = os.path.join(PROJECT_DIR, ".env.example")
DOCKER_COMPOSE_FILE = os.path.join(PROJECT_DIR, "docker-compose.yml")

# The standalone legacy Compose file used ``dark-*`` names.  Central Compose
# deliberately owns the dashboard as ``dashboard-*`` so it can share the apps
# host network with the APIs.  Resolve this once, before any readiness check.
MANAGED_COMPOSE = os.environ.get("DARK_DEPLOYER_MANAGED_COMPOSE") == "1"
CONTAINER_APP = "dashboard-app" if MANAGED_COMPOSE else "dark-app"
CONTAINER_MYSQL = "dashboard-mysql" if MANAGED_COMPOSE else "dark-mysql"

# Database credentials matching docker-compose.yml
DB_CONFIG = {
    "DB_HOST": "mysql",
    "DB_PORT": "3306",
    "DB_DATABASE": "dark",
    "DB_USERNAME": "dark",
    "DB_PASSWORD": "dark",
}

MYSQL_READY_TIMEOUT = 60  # seconds
APP_URL = "http://127.0.0.1:8081"

# ─── Helpers ────────────────────────────────────────────────────────────────────

class Colors:
    GREEN = "\033[92m"
    YELLOW = "\033[93m"
    RED = "\033[91m"
    CYAN = "\033[96m"
    BOLD = "\033[1m"
    RESET = "\033[0m"


def log_step(step_num, total, message):
    print(f"\n{Colors.CYAN}{Colors.BOLD}[{step_num}/{total}]{Colors.RESET} {message}")


def log_ok(message):
    print(f"  {Colors.GREEN}✔{Colors.RESET} {message}")


def log_warn(message):
    print(f"  {Colors.YELLOW}⚠{Colors.RESET} {message}")


def log_error(message):
    print(f"  {Colors.RED}✘{Colors.RESET} {message}")


def run(cmd, check=True, capture=False, cwd=PROJECT_DIR, timeout=300):
    """Run a shell command and return the CompletedProcess."""
    try:
        result = subprocess.run(
            cmd,
            shell=True,
            check=check,
            capture_output=capture,
            text=True,
            cwd=cwd,
            timeout=timeout,
        )
        return result
    except subprocess.CalledProcessError as e:
        if capture:
            log_error(f"Command failed: {cmd}")
            if e.stdout:
                print(f"    stdout: {e.stdout.strip()}")
            if e.stderr:
                print(f"    stderr: {e.stderr.strip()}")
        raise
    except subprocess.TimeoutExpired:
        log_error(f"Command timed out after {timeout}s: {cmd}")
        raise


def docker_exec(command, container=CONTAINER_APP):
    """Execute a command inside a Docker container."""
    return run(f"docker exec {container} bash -c '{command}'")


def check_command_exists(name):
    """Check if a system command is available."""
    return shutil.which(name) is not None


# ─── Steps ──────────────────────────────────────────────────────────────────────

TOTAL_STEPS = 9


def step_check_prerequisites():
    """Step 1: Verify Docker and docker-compose are installed."""
    log_step(1, TOTAL_STEPS, "Checking prerequisites...")

    missing = []

    if not check_command_exists("docker"):
        missing.append("docker")
    else:
        log_ok("Docker found")

    # docker-compose can be either standalone or a docker plugin
    has_compose = check_command_exists("docker-compose")
    if not has_compose:
        result = run("docker compose version", check=False, capture=True)
        has_compose = result.returncode == 0

    if not has_compose:
        missing.append("docker-compose")
    else:
        log_ok("docker-compose found")

    if missing:
        log_error(f"Missing required tools: {', '.join(missing)}")
        log_error("Please install them before running this script.")
        sys.exit(1)


def get_compose_cmd():
    """Return the appropriate docker-compose command."""
    if check_command_exists("docker-compose"):
        return "docker-compose"
    return "docker compose"


def step_setup_env():
    """Step 2: Create .env file and sync DB credentials with docker-compose.yml."""
    log_step(2, TOTAL_STEPS, "Setting up .env file...")

    if not os.path.exists(ENV_FILE):
        if not os.path.exists(ENV_EXAMPLE):
            log_error(".env.example not found. Cannot create .env file.")
            sys.exit(1)
        shutil.copy2(ENV_EXAMPLE, ENV_FILE)
        log_ok("Created .env from .env.example")
    else:
        log_ok(".env already exists")

    # Read current .env content
    with open(ENV_FILE, "r") as f:
        content = f.read()

    # Update DB credentials to match docker-compose.yml
    modified = False
    for key, value in DB_CONFIG.items():
        pattern = rf"^{key}=.*$"
        replacement = f"{key}={value}"
        new_content = re.sub(pattern, replacement, content, flags=re.MULTILINE)
        if new_content != content:
            content = new_content
            modified = True

    if modified:
        with open(ENV_FILE, "w") as f:
            f.write(content)
        log_ok("Updated .env DB credentials to match docker-compose.yml")
        log_warn(f"  DB_HOST={DB_CONFIG['DB_HOST']}, DB_DATABASE={DB_CONFIG['DB_DATABASE']}, "
                 f"DB_USERNAME={DB_CONFIG['DB_USERNAME']}")
    else:
        log_ok("DB credentials already in sync")

    # Sanitize .env: remove trailing semicolons and commas (invalid dotenv syntax)
    with open(ENV_FILE, "r") as f:
        lines = f.readlines()

    sanitized = False
    clean_lines = []
    for line in lines:
        stripped = line.rstrip("\n")
        # Skip comments and empty lines
        if stripped.startswith("#") or not stripped.strip():
            clean_lines.append(line)
            continue
        # Remove trailing semicolons and commas after values
        if stripped.endswith(";") or stripped.endswith(","):
            clean_lines.append(stripped.rstrip(";,") + "\n")
            sanitized = True
        else:
            clean_lines.append(line)

    if sanitized:
        with open(ENV_FILE, "w") as f:
            f.writelines(clean_lines)
        log_ok("Sanitized .env (removed trailing semicolons/commas)")


def step_start_containers():
    """Step 3: Start Docker containers."""
    log_step(3, TOTAL_STEPS, "Starting Docker containers...")

    compose_cmd = get_compose_cmd()
    run(f"{compose_cmd} up -d", cwd=PROJECT_DIR)
    log_ok(f"Containers started ({CONTAINER_APP}, {CONTAINER_MYSQL})")


def step_wait_mysql():
    """Step 4: Wait for MySQL to be ready to accept connections."""
    log_step(4, TOTAL_STEPS, "Waiting for MySQL to be ready...")

    start = time.time()
    while time.time() - start < MYSQL_READY_TIMEOUT:
        result = run(
            f'docker exec {CONTAINER_MYSQL} mysqladmin ping -u root -pdark --silent',
            check=False,
            capture=True,
        )
        if result.returncode == 0:
            log_ok(f"MySQL is ready ({int(time.time() - start)}s)")
            return
        time.sleep(2)
        print("  ⏳ Waiting...", end="\r")

    log_error(f"MySQL did not become ready within {MYSQL_READY_TIMEOUT}s")
    sys.exit(1)


def step_install_npm():
    """Step 5: Install NPM dependencies."""
    log_step(5, TOTAL_STEPS, "Installing NPM dependencies...")

    if check_command_exists("npm"):
        run("npm install", cwd=PROJECT_DIR)
        log_ok("NPM dependencies installed (leaflet)")
    else:
        log_warn("npm not found on host. Skipping NPM install.")
        log_warn("You may need to run 'npm install' manually.")


def step_install_composer():
    """Step 6: Install PHP dependencies via Composer inside the container."""
    log_step(6, TOTAL_STEPS, "Installing PHP dependencies (composer install)...")

    # Wait a moment for the app container to be fully ready
    time.sleep(3)

    # Check if the container is running
    result = run(
        f"docker inspect -f '{{{{.State.Running}}}}' {CONTAINER_APP}",
        check=False,
        capture=True,
    )
    if result.returncode != 0 or "true" not in result.stdout:
        log_error(f"Container '{CONTAINER_APP}' is not running.")
        sys.exit(1)

    docker_exec("cd /var/www/app && php composer.phar install --no-interaction --prefer-dist")
    log_ok("PHP dependencies installed")


def step_generate_key():
    """Step 7: Generate Laravel APP_KEY."""
    log_step(7, TOTAL_STEPS, "Generating Laravel APP_KEY...")

    docker_exec("cd /var/www/app && php artisan key:generate --force")
    log_ok("APP_KEY generated")


def step_migrate_seed():
    """Step 8: Run database migrations and seeders."""
    log_step(8, TOTAL_STEPS, "Running database migrations and seeders...")

    docker_exec("cd /var/www/app && php artisan migrate --step --seed --force")
    log_ok("Migrations and seeders executed")


def step_storage_link():
    """Step 9: Create storage symbolic link."""
    log_step(9, TOTAL_STEPS, "Creating storage symbolic link...")

    docker_exec("cd /var/www/app && php artisan storage:link --force")
    log_ok("Storage link created")


# ─── Reset ──────────────────────────────────────────────────────────────────────

def reset_environment():
    """Destroy all containers, volumes and start fresh."""
    print(f"\n{Colors.YELLOW}{Colors.BOLD}⚠  RESET MODE{Colors.RESET}")
    print("This will destroy all containers, volumes, and data.\n")

    confirmation = input("Are you sure? Type 'yes' to confirm: ")
    if confirmation.strip().lower() != "yes":
        print("Aborted.")
        sys.exit(0)

    compose_cmd = get_compose_cmd()
    print("\nStopping and removing containers and volumes...")
    run(f"{compose_cmd} down -v", cwd=PROJECT_DIR, check=False)
    log_ok("Containers and volumes removed")


# ─── Main ───────────────────────────────────────────────────────────────────────

def print_banner():
    print(f"""
{Colors.CYAN}{Colors.BOLD}╔══════════════════════════════════════════════╗
║        dARK Dashboard Web — Installer        ║
╚══════════════════════════════════════════════╝{Colors.RESET}
""")


def print_success():
    print(f"""
{Colors.GREEN}{Colors.BOLD}╔══════════════════════════════════════════════╗
║          ✔  Installation Complete!           ║
╚══════════════════════════════════════════════╝{Colors.RESET}

  {Colors.BOLD}Application URL:{Colors.RESET}  {APP_URL}/login

  {Colors.BOLD}Default credentials:{Colors.RESET}
  ┌──────────────────┬──────────────────────────────┐
  │ Profile          │ Email                        │
  ├──────────────────┼──────────────────────────────┤
  │ Administrator    │ admin@darkpid.com            │
  │ Institution      │ instituition@darkpid.com     │
  │ User             │ user@darkpid.com             │
  └──────────────────┴──────────────────────────────┘
  Password for all: {Colors.BOLD}password{Colors.RESET}

  {Colors.YELLOW}Tip:{Colors.RESET} To stop the application:
    docker compose down

  {Colors.YELLOW}Tip:{Colors.RESET} To reset everything and reinstall:
    python3 install.py --reset
""")


def main():
    parser = argparse.ArgumentParser(description="dARK Dashboard Web Installer")
    parser.add_argument(
        "--reset",
        action="store_true",
        help="Destroy all containers/volumes and reinstall from scratch",
    )
    args = parser.parse_args()

    print_banner()

    if args.reset:
        reset_environment()

    step_check_prerequisites()
    step_setup_env()
    if os.environ.get("DARK_DEPLOYER_MANAGED_COMPOSE") == "1":
        log_ok("Docker containers are managed by dark-deployer")
    else:
        step_start_containers()
    step_wait_mysql()
    step_install_npm()
    step_install_composer()
    step_generate_key()
    step_migrate_seed()
    step_storage_link()

    print_success()


if __name__ == "__main__":
    main()
