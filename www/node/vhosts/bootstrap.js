module.exports = new (function Vhosts(){

    const FileSystem    = require('fs');
    const Net           = require('net');
    const Tls           = require('tls');
	const Path          = require('path');
	const DS            = Path.sep;
	const DIR_ROOT      = __dirname+DS;
    var __config        = {};
    var __http          = null;
    var __https         = null;

    function __construct(){
        process.on('uncaughtException', (err, origin) => {
            console.error(err);
        });
        let configFile = DIR_ROOT+'vhosts.json';
        FileSystem.exists(configFile, (status) => {
            if(!status){ throw "configFile \""+configFile+"\" not found"; }
            __config = require(configFile);
            __config.ssl.key  = FileSystem.readFileSync(__config.ssl.key);
            __config.ssl.cert = FileSystem.readFileSync(__config.ssl.cert);
            __config.vhosts = __config.vhosts.map(function(v){
                v.serverName = v.serverName.map(function(v){
                    return new RegExp(v.replace(/\*/g, '(.*)'));
                });
                return v;
            });
            __startHttpServer(__config.http, () => {
                __startHttpsServer(__config.https, __config.ssl, () => {
                    console.log('<Vhosts:main> is ready');
                });
            });
        });
    }

    function __startHttpServer(port, callback){
        __http = Net.createServer(function(socket){
            __onSockerReceive(socket, false, true);
        }).on('error', function(err){
            if(err.code=='EADDRINUSE'){
                console.error('<Vhosts:http> error: Port '+port+' already in use');
            } else {
                console.error('<Vhosts:http> error: '+err.message);
            }
            __server.close();
        }).listen(port, function(){
            console.log('<Vhosts:http> is now listening port '+port);
            callback();
        });
    }

    function __startHttpsServer(port, ssl, callback){
        __https = Tls.createServer(ssl, function(socket){
            __onSockerReceive(socket, true, socket.authorized);
        }).on('error', function(err){
            if(err.code=='EADDRINUSE'){
                console.error('<Vhosts:https> error: Port '+port+' already in use');
            } else {
                console.error('<Vhosts:https> error: '+err.message);
            }
            __https.close();
        }).listen(port, function(){
            console.log('<Vhosts:https> is now listening port '+port);
            callback();
        });
    }

    function __onSockerReceive(socket, ssl, authorized){
        socket.on('data'   , function(data){ 
            let buffer = data.toString('binary');
            // extract host
            let start = buffer.indexOf("Host:");
            if(start>0){
                start += 5;
                let finish  = buffer.indexOf("\n", start);
                let host    = buffer.substring(start, finish).replace(/^[^a-zA-z0-9]+/, '').replace(/[^a-zA-z0-9]+$/, '');
                let forward = __resolve(host);
                if(typeof __config.routePort[forward]=='undefined'){
                    socket.close();
                } else {
                    __bridge(socket, data, __config.routePort[forward]);
                }
            }
        });
        socket.on('error'   , function(e){ socket.end(); });
        socket.on('timeout' , function(){ socket.end(); });        
        socket.on('end'     , function(){ socket.destroy(); });
    }

    function __resolve(host){
        var found = null;
        __config.vhosts.forEach(function(vhost){
            if(found==null){
                var match = false;
                vhost.serverName.forEach(function(reghost){
                    if(reghost.test(host)){
                        match = true;
                    }
                });
                if(match){
                    found = vhost.forward;
                }
            }
        });
        return found;
    }

    function __bridge(socket, data, forwardPort){
        var socketbridge = new Net.Socket();
        try{
            socketbridge.connect(forwardPort, '127.0.0.1', function(err){
                if(!err){
                    socket.on(        'data', function(data){ if(socketbridge!=null  ){ socketbridge.write(data); } });
                    socketbridge.on(  'data', function(data){ if(socket!=null        ){ socket.write(data);       } });
                    function unbridge(){
                        if(socketbridge!=null){
                            socketbridge.end();
                            socketbridge.destroy();
                        }
                        if(socket!=null){
                            socket.end();
                            socket.destroy();
                        }
                    }
                    socket.on('error'  , function(){ unbridge(); });
                    socket.on('timeout', function(){ unbridge(); });
                    socket.on('close'  , function(){ unbridge(); });
                    socket.on('error'  , function(){ unbridge(); });
                    socket.on('timeout', function(){ unbridge(); });
                    socket.on('close'  , function(){ unbridge(); });
                    socketbridge.write(data);
                } else {
                    socket.end();
                    socket.destroy();
                }
            });
        } catch(e){
            socket.end();
            socket.destroy();
        }
    }

    __construct();

})();
