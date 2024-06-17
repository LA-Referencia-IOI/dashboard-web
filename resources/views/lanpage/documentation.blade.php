<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Software Documentation</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: #333;
            color: #fff;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
            overflow-y: auto;
        }
        .sidebar h2 {
            text-align: center;
            color: #fff;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 10px;
            text-align: center;
        }
        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            display: block;
        }
        .sidebar ul li a:hover {
            background: #575757;
        }
        .submenu {
            display: none;
            padding-left: 20px;
        }
        .submenu li {
            text-align: left;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            background: #fff;
            flex: 1;
        }
        h1, h2, h3 {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Documentation</h2>
        <ul>
        <li class="nav-item">
                <a class="nav-link" href="{{route('site.index')}}"><b>Back to home</b></a>
              </li>
            <li>
                <a href="#get-started" onclick="toggleMenu('get-started-menu')">Get Started</a>
                <ul id="get-started-menu" class="submenu">
                    <li><a href="#deploy-local-node">Deploy a Local Node</a></li>
                    <li><a href="#integrating-dspace">Integrating with DSPACE</a></li>
                </ul>
            </li>
            <li>
                <a href="#our-network" onclick="toggleMenu('our-network-menu')">Our Network</a>
                <ul id="our-network-menu" class="submenu">
                    <li><a href="#overview">Overview</a></li>
                    <li><a href="#list-nodes">List of Nodes</a></li>
                    <li><a href="#topology">Topology</a></li>
                    <li><a href="#consensus-protocol">Consensus Protocol</a></li>
                    <li><a href="#gas-distribution">Gas Distribution</a></li>
                </ul>
            </li>
            <li>
                <a href="#concepts" onclick="toggleMenu('concepts-menu')">Concepts</a>
                <ul id="concepts-menu" class="submenu">
                    <li>
                        <a href="#dark" onclick="toggleMenu('dark-menu')">dARK</a>
                        <ul id="dark-menu" class="submenu">
                            <li><a href="#what-is-dark">What is dARK</a></li>
                            <li><a href="#naan">NAAN</a></li>
                            <li><a href="#resolver">Resolver</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#tutorials" onclick="toggleMenu('tutorials-menu')">Tutorials</a>
                <ul id="tutorials-menu" class="submenu">
                    <li><a href="#integrating-dspace">Integrating with DSPACE</a></li>
                    <li><a href="#instantiate-dark-node">How to Instantiate a dARK Node</a></li>
                </ul>
            </li>
            <li><a href="#faq">FAQ</a></li>
        </ul>
    </div>

    <div class="content">
        <section id="get-started">
            <h2>Get Started</h2>
            <section id="deploy-local-node">
                <h3>Deploy a Local Node</h3>
                <p>Instructions on how to deploy a local node.</p>
            </section>
            <section id="integrating-dspace">
                <h3>Integrating with DSPACE</h3>
                <p>Steps to integrate with DSPACE.</p>
            </section>
        </section>

        <section id="our-network">
            <h2>Our Network</h2>
            <section id="overview">
                <h3>Overview</h3>
                <p>Details about our network and how to connect to it.</p>
            </section>
            <section id="list-nodes">
                <h3>List of Nodes</h3>
                <p>List of nodes in our network.</p>
            </section>
            <section id="topology">
                <h3>Topology</h3>
                <p>Information about the network topology.</p>
            </section>
            <section id="consensus-protocol">
                <h3>Consensus Protocol</h3>
                <p>Explanation of the consensus protocol used in the network.</p>
            </section>
            <section id="gas-distribution">
                <h3>Gas Distribution</h3>
                <p>Details about gas distribution in the network.</p>
            </section>
        </section>

        <section id="concepts">
            <h2>Concepts</h2>
            <section id="dark">
                <h3>dARK</h3>
                <section id="what-is-dark">
                    <h4>What is dARK</h4>
                    <p>Explanation of what dARK is.</p>
                </section>
                <section id="naan">
                    <h4>NAAN</h4>
                    <p>Details about NAAN.</p>
                </section>
                <section id="resolver">
                    <h4>Resolver</h4>
                    <p>Information about the resolver.</p>
                </section>
            </section>
        </section>

        <section id="tutorials">
            <h2>Tutorials</h2>
            <section id="integrating-dspace">
                <h3>Integrating with DSPACE</h3>
                <p>Steps to integrate with DSPACE.</p>
            </section>
            <section id="instantiate-dark-node">
                <h3>How to Instantiate a dARK Node</h3>
                <p>Instructions on how to instantiate a dARK node.</p>
            </section>
        </section>

        <section id="faq">
            <h2>FAQ</h2>
            <p>Frequently Asked Questions about the software.</p>
        </section>
    </div>

    <script>
        function toggleMenu(id) {
            var menu = document.getElementById(id);
            if (menu.style.display === "block") {
                menu.style.display = "none";
            } else {
                menu.style.display = "block";
            }
        }
    </script>
</body>
</html>
