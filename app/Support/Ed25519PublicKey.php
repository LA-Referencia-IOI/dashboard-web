<?php

namespace App\Support;

use InvalidArgumentException;

final class Ed25519PublicKey
{
    /** Normalize a public key to base64 of its raw 32 bytes. */
    public static function normalize(string $value): string
    {
        $value = trim($value);
        if (strpos($value, "\n") !== false || strpos($value, "\r") !== false) {
            throw new InvalidArgumentException('Paste a single Ed25519 public key.');
        }
        if (strpos($value, 'ssh-ed25519 ') === 0) {
            $parts = preg_split('/\s+/', $value, 3);
            $blob = base64_decode($parts[1] ?? '', true);
            if ($blob === false || strlen($blob) !== 51 ||
                substr($blob, 0, 19) !== pack('N', 11) . 'ssh-ed25519' . pack('N', 32)) {
                throw new InvalidArgumentException('Invalid OpenSSH Ed25519 public key.');
            }
            $key = substr($blob, 19);
        } else {
            $key = base64_decode($value, true);
        }
        if ($key === false || strlen($key) !== 32 || $key === str_repeat("\0", 32)) {
            throw new InvalidArgumentException('Use an Ed25519 public key in OpenSSH or base64 format.');
        }
        return base64_encode($key);
    }
}
