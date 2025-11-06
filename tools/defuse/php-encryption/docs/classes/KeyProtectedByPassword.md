Class: Defuse\Crypto\KeyProtectedByPassword
============================================

The `KeyProtectedByPassword` class represents a key that is "locked" with
a password. In order to obtain an instance of `Key` that you can use for
encrypting and decrypting, a `KeyProtectedByPassword` must first be "unlocked"
by providing the correct password.

`KeyProtectedByPassword` provides an alternative to using the
`encryptWithPassword()`, `decryptWithPassword()`, `encryptFileWithPassword()`,
and `decryptFileWithPassword()` methods with several advantages:

- The slow and computationally-expensive key stretching is run only once when
  you unlock a `KeyProtectedByPassword` to obtain the `Key`.
- You do not have to keep the original password in memory to encrypt and decrypt
  things. After you've obtained the `Key` from a `KeyProtectedByPassword`, the
  password is no longer necessary.

Instance Methods
-----------------

### saveToAsciiSafeString()

**Description:**

Saves the protected key to a string of printable ASCII characters, which can be
loaded again into a `KeyProtectedByPassword` instance using
`KeyProtectedByPassword::loadFromAsciiSafeString()`.

**Parameters:**

This method does not take any parameters.

**Return value:**

Returns a string of printable ASCII characters representing this
`KeyProtectedByPassword` instance, which can be loaded back into an instance of
`KeyProtectedByPassword` using
`KeyProtectedByPassword::loadFromAsciiSafeString()`.

**Exceptions:**

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

**Side-effects and performance:**

None.

**Cautions:**

This method currently returns a hexadecimal string. You should not rely on this
behavior. For example, it may be improved in the future to return a base64
string.

### unlockKey($password)

**Description:**

Unlocks the password-protected key, obtaining a `Key` which can be used for
encryption and decryption.

**Parameters:**

1. `$password` is the password required to unlock this `KeyProtectedByPassword`
   to obtain the `Key`.

**Return value:**

If `$password` is the correct password, then this method returns an instance of
the `Key` class.

**Exceptions:**

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

- `Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException` is thrown if
  either the given `$password` is not the correct password for this
  `KeyProtectedByPassword` or the ciphertext stored internally by this object
  has been modified, i.e. it was accidentally corrupted or intentionally
  corrupted by an attacker. There is no way for the caller to distinguish
  between these two cases.

**Side-effects and performance:**

This method runs a small and very fast set of self-tests if it is the very first
time this method or one of the `Crypto` methods has been called. The performance
overhead is negligible and can be safely ignored in all applications.

**Cautions:**

PHP stack traces display (portions of) the arguments passed to methods on the
call stack. If an exception is thrown inside this call, and it is uncaught, the
value of `$password` may be leaked out to an attacker through the stack trace.
We recommend configuring PHP to never output stack traces (either displaying
them to the user or saving them to log files).

It is impossible in principle to distinguish between the case where you attempt
to unlock with the wrong password and the case where you attempt to unlock
a modified (corrupted) `KeyProtectedByPassword`. It is up to the caller how to
best deal with this ambiguity, as it depends on the application this library is
being used in. If in doubt, consult with a professional cryptographer.

### changePassword($current\_password, $new\_password)

**Description:**

Changes the password, so that calling `unlockKey` on this object in the future
will require you to pass `$new\_password` instead of the old password. It is
your responsibility to overwrite all stored copies of this
`KeyProtectedByPassword`. Any copies you leave lying around can still be
decrypted with the old password.

**Parameters:**

1. `$current\_password` is the password that this `KeyProtectedByPassword` is
   currently protected with.
2. `$new\_password` is the new password, which the `KeyProtectedByPassword` will
   be protected with once this operation completes.

**Return value:**

If `$current\_password` is the correct password, then this method updates itself
to be protected with the new password, and also returns itself.

**Exceptions:**

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

- `Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException` is thrown if
  either the given `$current\_password` is not the correct password for this
  `KeyProtectedByPassword` or the ciphertext stored internally by this object
  has been modified, i.e. it was accidentally corrupted or intentionally
  corrupted by an attacker. There is no way for the caller to distinguish
  between these two cases.

**Side-effects and performance:**

This method runs a small and very fast set of self-tests if it is the very first
time this method or one of the `Crypto` methods has been called. The performance
overhead is negligible and can be safely ignored in all applications.

**Cautions:**

PHP stack traces display (portions of) the arguments passed to methods on the
call stack. If an exception is thrown inside this call, and it is uncaught, the
value of `$password` may be leaked out to an attacker through the stack trace.
We recommend configuring PHP to never output stack traces (either displaying
them to the user or saving them to log files).

It is impossible in principle to distinguish between the case where you attempt
to unlock with the wrong password and the case where you attempt to unlock
a modified (corrupted) `KeyProtectedByPassword`. It is up to the caller how to
best deal with this ambiguity, as it depends on the application this library is
being used in. If in doubt, consult with a professional cryptographer.

**WARNING:** Because of the way `KeyProtectedByPassword` is implemented, knowing
`SHA256($password)` is enough to decrypt a `KeyProtectedByPassword`. To be
secure, your application MUST NOT EVER compute `SHA256($password)` and use or
store it for any reason. You must also make sure that other libraries your
application is using don't compute it either.

Static Methods
---------------

### KeyProtectedByPassword::createRandomPasswordProtectedKey($password)

**Description:**

Generates a new random key that's protected by the string `$password` and
returns an instance of `KeyProtectedByPassword`.

**Parameters:**

1. `$password` is the password used to protect the random key.

**Return value:**

Returns an instance of `KeyProtectedByPassword` containing a randomly-generated
encryption key that's protected by the password `$password`.

**Exceptions:**

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

**Side-effects and performance:**

This method runs a small and very fast set of self-tests if it is the very first
time this method or one of the `Crypto` methods has been called. The performance
overhead is negligible and can be safely ignored in all applications.

**Cautions:**

PHP stack traces display (portions of) the arguments passed to methods on the
call stack. If an exception is thrown inside this call, and it is uncaught, the
value of `$password` may be leaked out to an attacker through the stack trace.
We recommend configuring PHP to never output stack traces (either displaying
them to the user or saving them to log files).

Be aware that if you protecting multiple keys with the same password, an
attacker with write access to your system will be able to swap the protected
keys around so that the wrong key gets used next time it is unlocked. This could
lead to data being encrypted with the wrong key, perhaps one that the attacker
knows.

**WARNING:** Because of the way `KeyProtectedByPassword` is implemented, knowing
`SHA256($password)` is enough to decrypt a `KeyProtectedByPassword`. To be
secure, your application MUST NOT EVER compute `SHA256($password)` and use or
store it for any reason. You must also make sure that other libraries your
application is using don't compute it either.

### KeyProtectedByPassword::loadFromAsciiSafeString($saved\_key\_string)

**Description:**

Loads an instance of `KeyProtectedByPassword` that was saved to a string by
`saveToAsciiSafeString()`.

**Parameters:**

1. `$saved_key_string` is the string returned from `saveToAsciiSafeString()`
   when the original `KeyProtectedByPassword` instance was saved.

**Return value:**

Returns an instance of `KeyProtectedByPassword` representing the same
password-protected key as the one that was represented by the
`KeyProtectedByPassword` instance that got saved into `$saved_key_string` by
a call to `saveToAsciiSafeString()`.

**Exceptions:**

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

- `Defuse\Crypto\Exception\BadFormatException` is thrown whenever
  `$saved_key_string` does not represent a valid `KeyProtectedByPassword`
  instance.

**Side-effects and performance:**

None.

**Cautions:**

None.
