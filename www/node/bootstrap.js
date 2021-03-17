module.exports = new (function(){

    const Filesystem	= require('fs');
    const Path          = require('path');
    const Spawn         = require('child_process').spawn;
    const DS            = Path.sep;
    const DIR_ROOT      = __dirname+DS;
    const CONFIG        = require(DIR_ROOT+'bootstrap.json');
    
    CONFIG.forEach(function(v){

        if(typeof v.argv=='undefined'){ v.argv = []; }
        if(v.argv.length>0){
            console.log('Start "'+v.name+'" with args ('+v.argv.join(',')+')');
        } else {
            console.log('Start "'+v.name+'"');
        }
        let path = __dirname+v.script.replace(/\//g, DS);
        let directory = path.split(DS);
        directory.pop();
        directory = directory.join(DS)+DS;
        Spawn(
            process.argv[0],
            [__dirname+v.script.replace(/\//g, DS)].concat(v.argv),
            {
                cwd     : directory,
                stdio   : [null, 'pipe', 'inherit'],
                env     : process.env
            }
        );
    });

})();

