<?php

const ENCRYPTION_ALGO = 'aes-256-cbc';
const HMAC_ALGO = 'sha512';
const HMAC_KEY_SIZE = 32;

/**
 * Generates a secure IV compatible with all PHP versions.
 *
 * @param  int $length IV length.
 * @return string Generated IV.
 */
function generate_iv($length)
{
    if (function_exists('random_bytes')) {
        try {
            return random_bytes($length);
        } catch (Exception $e) {
            // Fallback to OpenSSL
        }
    }
    return openssl_random_pseudo_bytes($length);
}

/**
 * Generates a secure key from a passphrase using PBKDF2.
 *
 * @param  string $key Input passphrase.
 * @return string 32-byte derived key.
 */
function hash_key($key)
{
    return hash_pbkdf2('sha256', $key, 'secure_salt', 100000, 32, true);
}

/**
 * Encrypts a string using AES-256-CBC and HMAC-SHA512.
 *
 * @param  string $plaintext Plain text input.
 * @param  string $key       Encryption key.
 * @return string|false Encrypted binary data or false on failure.
 */
function aes_encrypt_string($plaintext, $key)
{
    $key = hash_key($key);
    $hmac_key = hash('sha256', $key . 'hmac', true);
    $iv = generate_iv(openssl_cipher_iv_length(ENCRYPTION_ALGO));

    $ciphertext = openssl_encrypt($plaintext, ENCRYPTION_ALGO, $key, OPENSSL_RAW_DATA, $iv);
    if ($ciphertext === false) {
        return false;
    }

    $hmac = hash_hmac(HMAC_ALGO, $iv . $ciphertext, $hmac_key, true);
    return base64_encode($iv . $hmac . $ciphertext);
}

/**
 * Decrypts an AES-256-CBC encrypted string and verifies integrity using HMAC-SHA512.
 *
 * @param  string $data Base64-encoded encrypted data.
 * @param  string $key  Decryption key.
 * @return string|false Plain text or false if authentication fails.
 */
function aes_decrypt_string($data, $key)
{
    $data = base64_decode($data);
    if ($data === false) {
        return false;
    }

    $key = hash_key($key);
    $hmac_key = hash('sha256', $key . 'hmac', true);
    $ivLength = openssl_cipher_iv_length(ENCRYPTION_ALGO);

    if (strlen($data) < $ivLength + 64) {
        return false;
    }

    $iv = substr($data, 0, $ivLength);
    $hmac = substr($data, $ivLength, 64);
    $ciphertext = substr($data, $ivLength + 64);

    if (!hash_equals(hash_hmac(HMAC_ALGO, $iv . $ciphertext, $hmac_key, true), $hmac)) {
        return false;
    }

    return openssl_decrypt($ciphertext, ENCRYPTION_ALGO, $key, OPENSSL_RAW_DATA, $iv);
}
