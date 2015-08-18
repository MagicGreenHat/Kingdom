[![npm package](https://img.shields.io/npm/v/describe-property.svg?style=flat-square)](https://www.npmjs.org/package/describe-property)
[![build status](https://img.shields.io/travis/mjackson/describe-property.svg?style=flat-square)](https://travis-ci.org/mjackson/describe-property)
[![dependency status](https://img.shields.io/david/mjackson/describe-property.svg?style=flat-square)](https://david-dm.org/mjackson/describe-property)
[![code climate](https://img.shields.io/codeclimate/github/mjackson/describe-property.svg?style=flat-square)](https://codeclimate.com/github/mjackson/describe-property)

[describe-property](https://github.com/mjackson/describe-property) is a property descriptor library that runs in both node.js and the browser. You use it to quickly generate property descriptors to use with [`Object.create`](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/create), [`Object.defineProperty`](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/defineProperty), and/or [`Object.defineProperties`](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/defineProperties).

### Example

```js
var d = require('describe-property');

function Person(firstName, surname) {
  this.firstName = firstName;
  this.surname = surname;
}

Object.defineProperties(Person.prototype, {

  // Methods can be passed directly.
  sayHi: d(function () {
    console.log('Hello, my name is', this.fullName);
  }),

  // Getters are defined using d.gs.
  fullName: d.gs(function () {
    return this.firstName + ' ' + this.surname;
  }),

  // Setters are defined as the second argument to d.gs.
  firstName: d.gs(function () {
    return this._firstName;
  }, function (value) {
    this._firstName = value.trim();
  })

});
```

By default property descriptors use ES5 attributes.

```js
{
  configurable: true,
  enumerable: false,
  writable: true
}
```

But any of these can be overridden using an object literal.

```js
d({
  enumerable: true,
  value: function () {
    // ...
  }
}); // => { configurable: true, enumerable: true, writable: true, value: function () {} }
```

### Installation

Using [npm](https://www.npmjs.org/):

    $ npm install describe-property

### Issues

Please file issues on the [issue tracker on GitHub](https://github.com/mjackson/describe-property/issues).

### Tests

To run the tests in node:

    $ npm install
    $ npm test

### Credits

This library was inspired by [@medikoo](https://github.com/medikoo)'s excellent [d](https://github.com/medikoo/d) library. It is intended to be a lighter-weight alternative with fewer features, but also only a single dependency.

### License

[MIT](http://opensource.org/licenses/MIT)
