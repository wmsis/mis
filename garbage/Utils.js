const CryptoJS = require("crypto-js");
const JSEncrypt = require('node-jsencrypt')
const crypto = require('crypto')
const NodeRSA = require('node-rsa');
const moment = require('moment');

exports.aes_encode = (key, plainText)=>{
    let newkey = CryptoJS.enc.Utf8.parse(key); //密钥必须是16位，且避免使用保留字符
    let encryptedData  = CryptoJS.AES.encrypt(plainText, newkey, {
        mode: CryptoJS.mode.ECB,
        padding: CryptoJS.pad.Pkcs7
    });
    return encryptedData.toString();
}

exports.aes_decode = (key, encryptedData)=>{
    let newkey = CryptoJS.enc.Utf8.parse(key);
    let decryptedData  = CryptoJS.AES.decrypt(encryptedData, newkey, {
        mode: CryptoJS.mode.ECB,
        padding: CryptoJS.pad.Pkcs7
    });
    let text = decryptedData.toString(CryptoJS.enc.Utf8);

    return text;
}

exports.aes_encode2 = (key, plainText)=>{
    var realKey = CryptoJS.SHA1(key);
    realKey = CryptoJS.SHA1(realKey).toString().substring(0, 32);
    let newkey = CryptoJS.enc.Hex.parse(realKey); //密钥必须是16位，且避免使用保留字符
    let encryptedData  = CryptoJS.AES.encrypt(plainText, newkey, {
        iv: '',
        mode: CryptoJS.mode.ECB,
        padding: CryptoJS.pad.Pkcs7
    });
    return encryptedData.toString();
}

exports.aes_encode3 = (key, plainText)=>{
    let iv = "";
    var clearEncoding = 'utf8';
    var cipherEncoding = 'base64';
    var cipherChunks = [];
    var cipher = crypto.createCipheriv('aes-128-ecb', key, iv);
    cipher.setAutoPadding(true);
    cipherChunks.push(cipher.update(plainText, clearEncoding, cipherEncoding));
    cipherChunks.push(cipher.final(cipherEncoding));
    return cipherChunks.join('');
}

exports.rsa_encode = (publicKey, plainText)=>{
    const obj = new JSEncrypt();
    obj.setPublicKey(publicKey);
    let encrypted = obj.encrypt(plainText);
    return encrypted;
};

exports.rsa_encode2 = (publicKey, plainText)=>{
    let key= new NodeRSA(publicKey);
    //key.generateKeyPair(1024);
    key.setOptions({encryptionScheme: 'pkcs1'})
    let encryptKey = key.encrypt(plainText, 'base64')
    return encryptKey;
};


exports.wait = async (ms) => {
    return new Promise(resolve => setTimeout(() => resolve(), ms));
};

exports.formatDateNumber = time => moment(time).format('YYYYMMDD');

exports.formatTime = time => moment(time).format('YYYY-MM-DD HH:mm:ss');

exports.formatDate = time => moment(time).format('YYYY-MM-DD');
