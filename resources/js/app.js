require('./bootstrap');
var crypto = require('crypto');
var Base64 = require('js-base64').Base64;
var serialize = require('php-serialize');



////////////////////////////////////////////////
/*
import crypto from 'crypto-js';

const str = "test";
const cryptoInfo = crypto.AES.encrypt(JSON.stringify({ str }), 'secret').toString();
console.log({ cryptoInfo });

const info2 = crypto.AES.decrypt(cryptoInfo, 'secret').toString(crypto.enc.Utf8);
console.log({ info2 });

const info3 = JSON.parse(info2);
console.log({ str: info3.str });
*/ /////////////////////////////////////////////////

/*
const data = "test";

import CryptoJS from "crypto-js";


let iv = CryptoJS.lib.WordArray.random(16),
    key = CryptoJS.enc.Utf8.parse(key1);
console.log(key1);
let options = {
    iv: iv,
    mode: CryptoJS.mode.CBC,
    padding: CryptoJS.pad.Pkcs7
};
let encrypted = CryptoJS.AES.encrypt(data, key1);
encrypted = encrypted.toString();
iv = CryptoJS.enc.Base64.stringify(iv);
let result = {
    iv: iv,
    value: encrypted,
    mac: CryptoJS.SHA1(iv + encrypted, key1).toString()
}
result = JSON.stringify(result);
result = CryptoJS.enc.Utf8.parse(result);
var js_encrypt = CryptoJS.enc.Base64.stringify(result);



console.log(js_encrypt);


var CryptoJS = require("crypto-js"); //replace thie with script tag in browser env

//encrypt


var parsedWordArray = CryptoJS.enc.Base64.parse(base64);
var parsedStr = parsedWordArray.toString(CryptoJS.enc.Utf8);
console.log("parsed:", parsedStr);

var rawStr = "test";
var wordArray = CryptoJS.enc.Utf8.parse(rawStr);
var base64 = CryptoJS.enc.Base64.stringify(wordArray, key1);
console.log('encrypted:', base64);
*/


const key1 = "base64:ZIEOZBtU/S9LPJZTHuRyiZ6q47pv+SvwwnLmB0vvfCI=";
var rawStr = "1234";
var test = CryptoJS.AES.encrypt(rawStr, key1);
test = test.toString();
var res = CryptoJS.SHA1(test);
res = JSON.stringify(res);
res = CryptoJS.enc.Utf8.parse(res);
var js_encrypt = CryptoJS.enc.Base64.stringify(res);
console.log(js_encrypt);
var wordArray = CryptoJS.enc.Utf8.parse(rawStr);
var base64 = CryptoJS.enc.Base64.stringify(wordArray, key1);
console.log('encrypted:', base64);