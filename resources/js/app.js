require('./bootstrap');


/*window.decrypt = (encrypted) => {
    let key = process.env.MIX_APP_KEY.substr(7);
    var encrypted_json = JSON.parse(atob(encrypted));
    return CryptoJS.AES.decrypt(encrypted_json.value, CryptoJS.enc.Base64.parse(key), {
        iv: CryptoJS.enc.Base64.parse(encrypted_json.iv)
    }).toString(CryptoJS.enc.Utf8);
};
*/
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
import crypto from 'crypto-js';


var encrypted = 'eyJpdiI6IlRxWjJlRDdXVW1MZys3NzYwQnIwZmc9PSIsInZhbHVlIjoiMy9pdGtzZ01YVUlCcDVSbm5SSE5ra1NaNkE1dGxDQnNZd1ZwU2dDdVRRND0iLCJtYWMiOiI2MDk5ZjNjYjE2ZWNlZGZjN2M5ZDdjM2ExNWJiM2FmMTZlNzI5MTUzOTM2ZTc3MzJiZWRlNGMzMDBlZjUwNDRlIn0=';
var key = "base64:7LN6K6PbrCuZtvJvvCWTH3YrBttz0Mul5Tad5wgaf8s=";

//var encrypted_json = JSON.parse(Base64.decode(encrypted));

const str = "test";
var encrypted_json2 = crypto.AES.encrypt(JSON.stringify({ str }), key).toString();
//console.log(encrypted_json2);
const info2 = crypto.AES.decrypt(encrypted_json2, key).toString(crypto.enc.Utf8);
//console.log({ info2 });
const info3 = JSON.parse(info2);
//console.log({ info2 });
// Now I try to decrypt it.
/*var decrypted = CryptoJS.AES.decrypt(encrypted_json.value, CryptoJS.enc.Base64.parse(key), {
    iv: CryptoJS.enc.Base64.parse(encrypted_json.iv)
});

console.log(decrypted.toString(CryptoJS.enc.Utf8));*/

this.key = key;
var encryptStr = "eyJpdiI6IlRxWjJlRDdXVW1MZys3NzYwQnIwZmc9PSIsInZhbHVlIjoiMy9pdGtzZ01YVUlCcDVSbm5SSE5ra1NaNkE1dGxDQnNZd1ZwU2dDdVRRND0iLCJtYWMiOiI2MDk5ZjNjYjE2ZWNlZGZjN2M5ZDdjM2ExNWJiM2FmMTZlNzI5MTUzOTM2ZTc3MzJiZWRlNGMzMDBlZjUwNDRlIn0=";
encryptStr = CryptoJS.enc.Base64.parse(encryptStr);
let encryptData = encryptStr.toString(CryptoJS.enc.Utf8);
encryptData = JSON.parse(encryptData);
let iv = CryptoJS.enc.Base64.parse(encryptData.iv);
var decrypted = CryptoJS.AES.decrypt(encryptData.value, CryptoJS.enc.Utf8.parse(this.key), {
    iv: iv,
    mode: CryptoJS.mode.CBC,
    padding: CryptoJS.pad.Pkcs7
});
decrypted = CryptoJS.enc.Utf8.stringify(decrypted);
console.log(decrypted);