<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Support\Ed25519PublicKey as PublicKey;

class Ed25519PublicKey implements Rule
{
    public function passes($attribute, $value)
    {
        try {
            PublicKey::normalize($value);
            return true;
        } catch (\Throwable $error) {
            return false;
        }
    }

    public function message()
    {
        return 'Use an Ed25519 public key in OpenSSH or base64 format. Never paste a private key.';
    }
}
