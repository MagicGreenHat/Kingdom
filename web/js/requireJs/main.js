requirejs.config({
    baseUrl: 'js/requireJs',
    paths: {
        client: '../client',
        jquery: '../lib/jquery-2.1.4.min',
        autobahn: '../lib/autobahn.min',
        when: '../lib/when'
    },
    shim: {
        autobahn: {
            deps: ['when']
        }
    }
});

requirejs(['client']);
