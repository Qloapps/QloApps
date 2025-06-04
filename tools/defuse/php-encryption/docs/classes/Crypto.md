Class: Defuse\Crypto\Crypto
============================

The `Crypto` class provides encryption and decryption of strings either using
a secret key or secret password. For encryption and decryption of large files,
see the `File` class.

This code for this class is in `src/Crypto.php`.

Instance Methods
-----------------

This class has no instance methods.

Static Methods
---------------

### Crypto::encrypt($plaintext, Key $key, $raw\_binary = false)

**Description:**

Encrypts a plaintext string using a secret key.

**Parameters:**

1. `$plaintext` is the string to encrypt.
2. `$key` is an instance of `Key` containing the secret key for encryption.
3. `$raw_binary` determines whether the output will be a byte string (true) or
  hex encoded (false, the default).

**Return value:**

Returns a ciphertext string representing `$plaintext` encrypted with the key
`$key`. Knowledge of `$key` is required in order to decrypt the ciphertext and
recover the plaintext.

**Exceptions:**

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

- `\TypeError` is thrown if the parameters are not of the expected types.

**Side-effects and performance:**

This method runs a small and very fast set of self-tests if it is the very first
time one of the `Crypto` methods has been called. The performance overhead is
negligible and can be safely ignored in all applications.

**Cautions:**

The ciphertext returned by this method is decryptable by anyone with knowledge
of the key `$key`. It is the caller's responsibility to keep `$key` secret.
Where `$key` should be stored is up to the caller and depends on the threat
model the caller is designing their application under. If you are unsure where
to store `$key`, consult with a professional cryptographer to get help designing
your application.

Please note that **encryption does not, and is not intended to, hide the
*length* of the data being encrypted.** For example, it is not safe to encrypt
a field in which only a small number of different-length values are possible
(e.g. "male" or "female") since it would be possible to tell what the plaintext
is by looking at the length of the ciphertext. In order to do this safely, it is
your responsibility to, before encrypting, pad the data out to the length of the
longest string that will ever be encrypted. This way, all plaintexts are the
same length, and no information about the plaintext can be gleaned from the
length of the ciphertext.

### Crypto::decrypt($ciphertext, Key $key, $raw\_binary = false)

**Description:**

Decrypts a ciphertext string using a secret key.

**Parameters:**

1. `$ciphertext` is the ciphertext to be decrypted.
2. `$key` is an instance of `Key` containing the secret key for decryption.
3. `$raw_binary` must have the same value as the `$raw_binary` given to the
   call to `encrypt()` that generated `$ciphertext`.

**Return value:**

If the decryption succeeds, returns a string containing the same value as the
string that was passed to `encrypt()` when `$ciphertext` was produced. Upon
a successful return, the caller can be assured that `$ciphertext` could not have
been produced except by someone with knowledge of `$key`.

**Exceptions:**

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

- `Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException` is thrown if
  the `$key` is not the correct key for the given ciphertext, or if the
  ciphertext has been modified (possibly maliciously). There is no way to
  distinguish between these two cases.

- `\TypeError` is thrown if the parameters are not of the expected types.

**Side-effects and performance:**

This method runs a small and very fast set of self-tests if it is the very first
time one of the `Crypto` methods has been called. The performance overhead is
negligible and can be safely ignored in all applications.

**Cautions:**

It is impossible in principle to distinguish between the case where you attempt
to decrypt with the wrong key and the case where you attempt to decrypt
a modified (corrupted) ciphertext. It is up to the caller how to best deal with
this ambiguity, as it depends on the application this library is being used in.
If in doubt, consult with a professional cryptographer.

### Crypto::encryptWithPassword($plaintext, $password, $raw\_binary = false)

**Description:**

Encrypts a plaintext string using a secret password.

**Parameters:**

1. `$plaintext` is the string to encrypt.
2. `$password` is a string containing the secret password used for encryption.
3. `$raw_binary` determines whether the output will be a byte string (true) or
  hex encoded (false, the default).

**Return value:**

Returns a ciphertext string representing `$plaintext` encrypted with a key
derived from `$password`. Knowledge of `$password` is required in order to
decrypt the ciphertext and recover the plaintext.

**Exceptions:**

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

- `\TypeError` is thrown if the parameters are not of the expected types.

**Side-effects and performance:**

This method is intentionally slow, using a lot of CPU resources for a fraction
of a second. It applies key stretching to the password in order to make password
guessing attacks more computationally expensive. If you need a faster way to
encrypt multiple ciphertexts under the same password, see the
`KeyProtectedByPassword` class.

This method runs a small and very fast set of self-tests if it is the very first
time one of the `Crypto` methods has been called. The performance overhead is
negligible and can be safely ignored in all applications.

**Cautions:**

PHP stack traces display (portions of) the arguments passed to methods on the
call stack. If an exception is thrown inside this call, and it is uncaught, the
value of `$password` may be leaked out to an attacker through the stack trace.
We recommend configuring PHP to never output stack traces (either displaying
them to the user or saving them to log files).

### Crypto::decryptWithPassword($ciphertext, $password, $raw\_binary = false)

**Description:**

Decrypts a ciphertext string using a secret password.

**Parameters:**

1. `$ciphertext` is the ciphertext to be decrypted.
2. `$password` is a string containing the secret password used for decryption.
3. `$raw_binary` must have the same value as the `$raw_binary` given to the
   call to `encryptWithPassword()` that generated `$ciphertext`.

**Return value:**

If the decryption succeeds, returns a string containing the same value as the
string that was passed to `encryptWithPassword()` when `$ciphertext` was
produced. Upon a successful return, the caller can be assured that `$ciphertext`
could not have been produced except by someone with knowledge of `$password`.

**Exceptions:**

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

- `Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException` is thrown if
  the `$password` is not the correct password for the given ciphertext, or if
  the ciphertext has been modified (possibly maliciously). There is no way to
  distinguish between these two cases.

- `\TypeError` is thrown if the parameters are not of the expected types.

**Side-effects:**

This method is intentionally slow. It applies key stretching to the password in
order to make password guessing attacks more computationally expensive. If you
need a faster way to encrypt multiple ciphertexts under the same password, see
the `KeyProtectedByPassword` class.

This method runs a small and very fast set of self-tests if it is the very first
time one of the `Crypto` methods has been called. The performance overhead is
negligible and can be safely ignored in all applications.

**Cautions:**

PHP stack traces display (portions of) the arguments passed to methods on the
call stack. If an exception is thrown inside this call, and it is uncaught, the
value of `$password` may be leaked out to an attacker through the stack trace.
We recommend configuring PHP to never output stack traces (either displaying
them to the user or saving them to log files).

It is impossible in principle to distinguish between the case where you attempt
to decrypt with the wrong password and the case where you attempt to decrypt
a modified (corrupted) ciphertext. It is up to the caller how to best deal with
this ambiguity, as it depends on the application this library is being used in.
If in doubt, consult with a professional cryptographer.

### Crypto::legacyDecrypt($ciphertext, $key)

**Description:**

Decrypts a ciphertext produced by version 1 of this library so that the
plaintext can be re-encrypted into a version 2 ciphertext. See [Upgrading from
v1.2](../UpgradingFromV1.2.md).

**Parameters:**

1. `$ciphertext` is a ciphertext produced by version 1.x of this library.
2. `$key` is a 16-byte string (*not* a Key object) containing the key that was
   used with version 1.x of this library to produce `$ciphertext`.

**Return value:**

If the decryption succeeds, returns the string that was encrypted to make
`$ciphertext` by version 1.x of this library. Upon a successful return, the
caller can be assured that `$ciphertext` could not have been produced except by
someone with knowledge of `$key`.

**Exceptions:**

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

- `Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException` is thrown if
  the `$key` is not the correct key for the given ciphertext, or if the
  ciphertext has been modified (possibly maliciously). There is no way to
  distinguish between these two cases.

- `\TypeError` is thrown if the parameters are not of the expected types.

**Side-effects:**

This method runs a small and very fast set of self-tests if it is the very first
time one of the `Crypto` methods has been called. The performance overhead is
negligible and can be safely ignored in all applications.

**Cautions:**

PHP stack traces display (portions of) the arguments passed to methods on the
call stack. If an exception is thrown inside this call, and it is uncaught, the
value of `$key` may be leaked out to an attacker through the stack trace. We
recommend configuring PHP to never output stack traces (either displaying them
to the user or saving them to log files).

It is impossible in principle to distinguish between the case where you attempt
to decrypt with the wrong key and the case where you attempt to decrypt
a modified (corrupted) ciphertext. It is up to the caller how to best deal with
this ambiguity, as it depends on the application this library is being used in.
If in doubt, consult with a professional cryptographer.

