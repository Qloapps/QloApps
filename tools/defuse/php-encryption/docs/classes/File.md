Class: Defuse\Crypto\File
==========================

Instance Methods
-----------------

This class has no instance methods.

Static Methods
---------------

### File::encryptFile($inputFilename, $outputFilename, Key $key)

**Description:**

Encrypts a file using a secret key.

**Parameters:**

1. `$inputFilename` is the path to a file containing the plaintext to encrypt.
2. `$outputFilename` is the path to save the ciphertext file.
3. `$key` is an instance of `Key` containing the secret key for encryption.

**Behavior:**

Encrypts the contents of the input file, writing the result to the output file.
If the output file already exists, it is overwritten.

**Return value:**

Does not return a value.

**Exceptions:**

- `Defuse\Crypto\Exception\IOException` is thrown if there is an I/O error.

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

**Side-effects and performance:**

None.

**Cautions:**

The ciphertext output by this method is decryptable by anyone with knowledge of
the key `$key`. It is the caller's responsibility to keep `$key` secret. Where
`$key` should be stored is up to the caller and depends on the threat model the
caller is designing their application under. If you are unsure where to store
`$key`, consult with a professional cryptographer to get help designing your
application.

Please note that **encryption does not, and is not intended to, hide the
*length* of the data being encrypted.** For example, it is not safe to encrypt
a field in which only a small number of different-length values are possible
(e.g. "male" or "female") since it would be possible to tell what the plaintext
is by looking at the length of the ciphertext. In order to do this safely, it is
your responsibility to, before encrypting, pad the data out to the length of the
longest string that will ever be encrypted. This way, all plaintexts are the
same length, and no information about the plaintext can be gleaned from the
length of the ciphertext.

### File::decryptFile($inputFilename, $outputFilename, Key $key)

**Description:**

Decrypts a file using a secret key.

**Parameters:**

1. `$inputFilename` is the path to a file containing the ciphertext to decrypt.
2. `$outputFilename` is the path to save the decrypted plaintext file.
3. `$key` is an instance of `Key` containing the secret key for decryption.

**Behavior:**

Decrypts the contents of the input file, writing the result to the output file.
If the output file already exists, it is overwritten.

**Return value:**

Does not return a value.

**Exceptions:**

- `Defuse\Crypto\Exception\IOException` is thrown if there is an I/O error.

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

- `Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException` is thrown if
  the `$key` is not the correct key for the given ciphertext, or if the
  ciphertext has been modified (possibly maliciously). There is no way to
  distinguish between these two cases.

**Side-effects and performance:**

The input ciphertext is processed in two passes. The first pass verifies the
integrity and the second pass performs the actual decryption of the file and
writing to the output file. This is done in a streaming manner so that only
a small part of the file is ever loaded into memory at a time.

**Cautions:**

Be aware that when `Defuse\Crypto\WrongKeyOrModifiedCiphertextException` is
thrown, some partial plaintext data may have been written to the output. Any
plaintext data that is output is guaranteed to be a prefix of the original
plaintext (i.e. at worst it was truncated). This can only happen if an attacker
modifies the input between the first pass (integrity check) and the second pass
(decryption) over the file.

It is impossible in principle to distinguish between the case where you attempt
to decrypt with the wrong key and the case where you attempt to decrypt
a modified (corrupted) ciphertext. It is up to the caller how to best deal with
this ambiguity, as it depends on the application this library is being used in.
If in doubt, consult with a professional cryptographer.

### File::encryptFileWithPassword($inputFilename, $outputFilename, $password)

**Description:**

Encrypts a file with a password.

**Parameters:**

1. `$inputFilename` is the path to a file containing the plaintext to encrypt.
2. `$outputFilename` is the path to save the ciphertext file.
3. `$password` is the password used for decryption.

**Behavior:**

Encrypts the contents of the input file, writing the result to the output file.
If the output file already exists, it is overwritten.

**Return value:**

Does not return a value.

**Exceptions:**

- `Defuse\Crypto\Exception\IOException` is thrown if there is an I/O error.

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

**Side-effects and performance:**

This method is intentionally slow, using a lot of CPU resources for a fraction
of a second. It applies key stretching to the password in order to make password
guessing attacks more computationally expensive. If you need a faster way to
encrypt multiple ciphertexts under the same password, see the
`KeyProtectedByPassword` class.

**Cautions:**

PHP stack traces display (portions of) the arguments passed to methods on the
call stack. If an exception is thrown inside this call, and it is uncaught, the
value of `$password` may be leaked out to an attacker through the stack trace.
We recommend configuring PHP to never output stack traces (either displaying
them to the user or saving them to log files).

Please note that **encryption does not, and is not intended to, hide the
*length* of the data being encrypted.** For example, it is not safe to encrypt
a field in which only a small number of different-length values are possible
(e.g. "male" or "female") since it would be possible to tell what the plaintext
is by looking at the length of the ciphertext. In order to do this safely, it is
your responsibility to, before encrypting, pad the data out to the length of the
longest string that will ever be encrypted. This way, all plaintexts are the
same length, and no information about the plaintext can be gleaned from the
length of the ciphertext.

### File::decryptFileWithPassword($inputFilename, $outputFilename, $password)

**Description:**

Decrypts a file with a password.

**Parameters:**

1. `$inputFilename` is the path to a file containing the ciphertext to decrypt.
2. `$outputFilename` is the path to save the decrypted plaintext file.
3. `$password` is the password used for decryption.

**Behavior:**

Decrypts the contents of the input file, writing the result to the output file.
If the output file already exists, it is overwritten.

**Return value:**

Does not return a value.

**Exceptions:**

- `Defuse\Crypto\Exception\IOException` is thrown if there is an I/O error.

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

- `Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException` is thrown if
  the `$password` is not the correct key for the given ciphertext, or if the
  ciphertext has been modified (possibly maliciously). There is no way to
  distinguish between these two cases.

**Side-effects and performance:**

This method is intentionally slow, using a lot of CPU resources for a fraction
of a second. It applies key stretching to the password in order to make password
guessing attacks more computationally expensive. If you need a faster way to
encrypt multiple ciphertexts under the same password, see the
`KeyProtectedByPassword` class.

The input ciphertext is processed in two passes. The first pass verifies the
integrity and the second pass performs the actual decryption of the file and
writing to the output file. This is done in a streaming manner so that only
a small part of the file is ever loaded into memory at a time.

**Cautions:**

PHP stack traces display (portions of) the arguments passed to methods on the
call stack. If an exception is thrown inside this call, and it is uncaught, the
value of `$password` may be leaked out to an attacker through the stack trace.
We recommend configuring PHP to never output stack traces (either displaying
them to the user or saving them to log files).

Be aware that when `Defuse\Crypto\WrongKeyOrModifiedCiphertextException` is
thrown, some partial plaintext data may have been written to the output. Any
plaintext data that is output is guaranteed to be a prefix of the original
plaintext (i.e. at worst it was truncated). This can only happen if an attacker
modifies the input between the first pass (integrity check) and the second pass
(decryption) over the file.

It is impossible in principle to distinguish between the case where you attempt
to decrypt with the wrong password and the case where you attempt to decrypt
a modified (corrupted) ciphertext. It is up to the caller how to best deal with
this ambiguity, as it depends on the application this library is being used in.
If in doubt, consult with a professional cryptographer.

### File::encryptResource($inputHandle, $outputHandle, Key $key)

**Description:**

Encrypts a resource (stream) with a secret key.

**Parameters:**

1. `$inputHandle` is a handle to a resource (like a file pointer) containing the
   plaintext to encrypt.
2. `$outputHandle` is a handle to a resource (like a file pointer) that the
   ciphertext will be written to.
3. `$key` is an instance of `Key` containing the secret key for encryption.

**Behavior:**

Encrypts the data read from the input stream and writes it to the output stream.

**Return value:**

Does not return a value.

**Exceptions:**

- `Defuse\Crypto\Exception\IOException` is thrown if there is an I/O error.

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

**Side-effects and performance:**

None.

**Cautions:**

The ciphertext output by this method is decryptable by anyone with knowledge of
the key `$key`. It is the caller's responsibility to keep `$key` secret. Where
`$key` should be stored is up to the caller and depends on the threat model the
caller is designing their application under. If you are unsure where to store
`$key`, consult with a professional cryptographer to get help designing your
application.

Please note that **encryption does not, and is not intended to, hide the
*length* of the data being encrypted.** For example, it is not safe to encrypt
a field in which only a small number of different-length values are possible
(e.g. "male" or "female") since it would be possible to tell what the plaintext
is by looking at the length of the ciphertext. In order to do this safely, it is
your responsibility to, before encrypting, pad the data out to the length of the
longest string that will ever be encrypted. This way, all plaintexts are the
same length, and no information about the plaintext can be gleaned from the
length of the ciphertext.

### File::decryptResource($inputHandle, $outputHandle, Key $key)

**Description:**

Decrypts a resource (stream) with a secret key.

**Parameters:**

1. `$inputHandle` is a handle to a file-backed resource containing the
   ciphertext to decrypt. It must be a file not a network stream or standard
   input.
2. `$outputHandle` is a handle to a resource (like a file pointer) that the
   plaintext will be written to.
3. `$key` is an instance of `Key` containing the secret key for decryption.

**Behavior:**

Decrypts the data read from the input stream and writes it to the output stream.

**Return value:**

Does not return a value.

**Exceptions:**

- `Defuse\Crypto\Exception\IOException` is thrown if there is an I/O error.

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

- `Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException` is thrown if
  the `$key` is not the correct key for the given ciphertext, or if the
  ciphertext has been modified (possibly maliciously). There is no way to
  distinguish between these two cases.

**Side-effects and performance:**

The input ciphertext is processed in two passes. The first pass verifies the
integrity and the second pass performs the actual decryption of the file and
writing to the output file. This is done in a streaming manner so that only
a small part of the file is ever loaded into memory at a time.

**Cautions:**

Be aware that when `Defuse\Crypto\WrongKeyOrModifiedCiphertextException` is
thrown, some partial plaintext data may have been written to the output. Any
plaintext data that is output is guaranteed to be a prefix of the original
plaintext (i.e. at worst it was truncated). This can only happen if an attacker
modifies the input between the first pass (integrity check) and the second pass
(decryption) over the file.

It is impossible in principle to distinguish between the case where you attempt
to decrypt with the wrong key and the case where you attempt to decrypt
a modified (corrupted) ciphertext. It is up to the caller how to best deal with
this ambiguity, as it depends on the application this library is being used in.
If in doubt, consult with a professional cryptographer.

### File::encryptResourceWithPassword($inputHandle, $outputHandle, $password)

**Description:**

Encrypts a resource (stream) with a password.

**Parameters:**

1. `$inputHandle` is a handle to a resource (like a file pointer) containing the
   plaintext to encrypt.
2. `$outputHandle` is a handle to a resource (like a file pointer) that the
   ciphertext will be written to.
3. `$password` is the password used for encryption.

**Behavior:**

Encrypts the data read from the input stream and writes it to the output stream.

**Return value:**

Does not return a value.

**Exceptions:**

- `Defuse\Crypto\Exception\IOException` is thrown if there is an I/O error.

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

**Side-effects and performance:**

This method is intentionally slow, using a lot of CPU resources for a fraction
of a second. It applies key stretching to the password in order to make password
guessing attacks more computationally expensive. If you need a faster way to
encrypt multiple ciphertexts under the same password, see the
`KeyProtectedByPassword` class.

**Cautions:**

PHP stack traces display (portions of) the arguments passed to methods on the
call stack. If an exception is thrown inside this call, and it is uncaught, the
value of `$password` may be leaked out to an attacker through the stack trace.
We recommend configuring PHP to never output stack traces (either displaying
them to the user or saving them to log files).

Please note that **encryption does not, and is not intended to, hide the
*length* of the data being encrypted.** For example, it is not safe to encrypt
a field in which only a small number of different-length values are possible
(e.g. "male" or "female") since it would be possible to tell what the plaintext
is by looking at the length of the ciphertext. In order to do this safely, it is
your responsibility to, before encrypting, pad the data out to the length of the
longest string that will ever be encrypted. This way, all plaintexts are the
same length, and no information about the plaintext can be gleaned from the
length of the ciphertext.

### File::decryptResourceWithPassword($inputHandle, $outputHandle, $password)

**Description:**

Decrypts a resource (stream) with a password.

**Parameters:**

1. `$inputHandle` is a handle to a file-backed resource containing the
   ciphertext to decrypt. It must be a file not a network stream or standard
   input.
2. `$outputHandle` is a handle to a resource (like a file pointer) that the
   plaintext will be written to.
3. `$password` is the password used for decryption.

**Behavior:**

Decrypts the data read from the input stream and writes it to the output stream.

**Return value:**

Does not return a value.

**Exceptions:**

- `Defuse\Crypto\Exception\IOException` is thrown if there is an I/O error.

- `Defuse\Crypto\Exception\EnvironmentIsBrokenException` is thrown either when
  the platform the code is running on cannot safely perform encryption for some
  reason (e.g. it lacks a secure random number generator), or the runtime tests
  detected a bug in this library.

- `Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException` is thrown if
  the `$password` is not the correct key for the given ciphertext, or if the
  ciphertext has been modified (possibly maliciously). There is no way to
  distinguish between these two cases.

**Side-effects and performance:**

This method is intentionally slow, using a lot of CPU resources for a fraction
of a second. It applies key stretching to the password in order to make password
guessing attacks more computationally expensive. If you need a faster way to
encrypt multiple ciphertexts under the same password, see the
`KeyProtectedByPassword` class.

The input ciphertext is processed in two passes. The first pass verifies the
integrity and the second pass performs the actual decryption of the file and
writing to the output file. This is done in a streaming manner so that only
a small part of the file is ever loaded into memory at a time.

**Cautions:**

PHP stack traces display (portions of) the arguments passed to methods on the
call stack. If an exception is thrown inside this call, and it is uncaught, the
value of `$password` may be leaked out to an attacker through the stack trace.
We recommend configuring PHP to never output stack traces (either displaying
them to the user or saving them to log files).

Be aware that when `Defuse\Crypto\WrongKeyOrModifiedCiphertextException` is
thrown, some partial plaintext data may have been written to the output. Any
plaintext data that is output is guaranteed to be a prefix of the original
plaintext (i.e. at worst it was truncated). This can only happen if an attacker
modifies the input between the first pass (integrity check) and the second pass
(decryption) over the file.

It is impossible in principle to distinguish between the case where you attempt
to decrypt with the wrong password and the case where you attempt to decrypt
a modified (corrupted) ciphertext. It is up to the caller how to best deal with
this ambiguity, as it depends on the application this library is being used in.
If in doubt, consult with a professional cryptographer.
