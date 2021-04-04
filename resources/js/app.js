require('./bootstrap');
var Base64 = require('js-base64').Base64;
const crypto = require('crypto');
const php = require('php-serialize');
const BASE_64_PREFIX = 'base64:';

var apiKey = "base64:ZIEOZBtU/S9LPJZTHuRyiZ6q47pv+SvwwnLmB0vvfCI=";
var value = "12345";
if (typeof apiKey === 'string' && apiKey.startsWith(BASE_64_PREFIX)) {
    apiKey = Buffer.from(apiKey.replace(BASE_64_PREFIX, ''), 'base64');
}

const iv = crypto.randomBytes(16);

const cipher = crypto.createCipheriv('AES-256-CBC', apiKey, iv);
let payloadValue = cipher.update(php.serialize(value), 'utf8', 'base64');
payloadValue += cipher.final('base64');

const ivStr = new Buffer(iv).toString('base64');
const hmac = crypto.createHmac('sha256', apiKey);

const mac = hmac.update(ivStr + payloadValue).digest('hex');

var res1 = new Buffer(JSON.stringify({
    iv: ivStr,
    value: payloadValue,
    mac: mac
})).toString('base64');

console.log(res1);