var objectAssign = require('object-assign');

function describeProperty(descriptor) {
  if (typeof descriptor === 'function')
    descriptor = { value: descriptor };

  // Use ES5 defaults.
  var defaults = {
    configurable: true,
    enumerable: false
  };

  if (descriptor.get == null && descriptor.set == null)
    defaults.writable = true;

  return objectAssign(defaults, descriptor);
}

describeProperty.gs = function (get, set) {
  var descriptor = {
    get: get
  };

  if (typeof set === 'function')
    descriptor.set = set;

  return describeProperty(descriptor);
};

module.exports = describeProperty;
