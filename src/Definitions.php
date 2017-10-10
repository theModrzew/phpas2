<?php
/**
 * This file contains definionions under PhpAS2 namespace
 *
 * Only most popular cipher types are provided here
 */

namespace PhpAS2;

/*
 * MDN types
 */
const MDN_SYNC  = 0;
const MDN_ASYNC = 1;
const MDN_SMTP  = 2;

/*
 * Algorithms
 */
const ALG_SHA1    = 'sha1';
const ALG_SHA2    = 'sha2';
const ALG_SHA256  = 'sha256';

/*
 * Cipher methods
 */

// Data Encryption Standard
const CPR_DES     = 'des';
const CPR_3DES    = '3des';

// Advanced Encryption Standard
const CPR_AES128  = 'aes128';
const CPR_AES192  = 'aes192';
const CPR_AES256  = 'aes256';

// Rivest Cipher
const CPR_RC2     = 'rc2';
const CPR_RC2_40  = 'rc2-40';
const CPR_RC2_64  = 'rc2-64';
const CPR_RC2_128 = 'rc2-128';
const CPR_RC4     = 'rc4';
const CPR_RC4_40  = 'rc4-40';

// Blowfish algorithm
const CPR_BLWFISH = 'blowfish';
