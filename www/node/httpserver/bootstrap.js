module.export = new (function ServeurHTTP(port){
	
	const Filesystem	= require('fs');
	const Path          = require('path');
	const DS            = Path.sep;
	const DIR_ROOT      = __dirname+DS;
	const DIR_WWW 		= DIR_ROOT+'www'+DS;
	const Http 			= require('http');
	
	const Mime 			= {
		'.aac' :'audio/aac',
		'.abw' :'application/x-abiword',
		'.arc' :'application/octet-stream',
		'.avi' :'video/x-msvideo',
		'.azw' :'application/vnd.amazon.ebook',
		'.bin' :'application/octet-stream',
		'.bmp' :'image/bmp',
		'.bz'  :'application/x-bzip',
		'.bz2' :'application/x-bzip2',
		'.csh' :'application/x-csh',
		'.css' :'text/css',
		'.csv' :'text/csv',
		'.doc' :'application/msword',
		'.docx':'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'.eot' :'application/vnd.ms-fontobject',
		'.epub':'application/epub+zip',
		'.gif' :'image/gif',
		'.htm' :'text/html',
		'.html':'text/html',
		'.ico' :'image/x-icon',
		'.ics' :'text/calendar',
		'.jar' :'application/java-archive',
		'.jpeg':'image/jpeg',
		'.jpg' :'image/jpeg',
		'.js'  :'application/javascript',
		'.json':'application/json',
		'.mid' :'audio/midi',
		'.midi':'audio/midi',
		'.mp3' :'audio/mpeg3',
		'.mpeg':'video/mpeg',
		'.mpkg':'application/vnd.apple.installer+xml',
		'.odp' :'application/vnd.oasis.opendocument.presentation',
		'.ods' :'application/vnd.oasis.opendocument.spreadsheet',
		'.odt' :'application/vnd.oasis.opendocument.text',
		'.oga' :'audio/ogg',
		'.ogv' :'video/ogg',
		'.ogx' :'application/ogg',
		'.otf' :'font/otf',
		'.png' :'image/png',
		'.pdf' :'application/pdf',
		'.ppt' :'application/vnd.ms-powerpoint',
		'.pptx' :'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'.rar' :'application/x-rar-compressed',
		'.rtf' :'application/rtf',
		'.sh'  :'application/x-sh',
		'.svg' :'image/svg+xml',
		'.swf' :'application/x-shockwave-flash',
		'.tar' :'application/x-tar',
		'.tif' :'image/tiff',
		'.tiff':'image/tiff',
		'.ts'  :'application/typescript',
		'.ttf' :'font/ttf',
		'.vsd' :'application/vnd.visio',
		'.wav' :'audio/x-wav',
		'.weba':'audio/webm',
		'.webm':'video/webm',
		'.webp':'image/webp',
		'.woff':'font/woff',
		'.woff2':'font/woff2',
		'.xhtml':'application/xhtml+xml',
		'.xls' :'application/vnd.ms-excel',
		'.xlsx':'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'.xml' :'application/xml',
		'.xul' :'application/vnd.mozilla.xul+xml',
		'.zip' :'application/zip',
		'.3gp' :'video/3gpp',
		'.3g2' :'video/3gpp2',
		'.7z' :'application/x-7z-compressed'
	}
	
    process.on('uncaughtException', (err, origin) => {
        console.error(err);
    });

	Http.createServer(function (req, res){
		
		// try find file
		let url = req.url;
		let path = DIR_WWW+(url==''?'/':url).replace(/^[\/]+/, '').replace('/', DS);
		let localUrl = 'http://'+req.headers.host;
		
		Filesystem.lstat(path, function(err, lstat){
			if(err){
				res.writeHead(404, {
					'Content-Length': 78,
					'Content-Type'	: Mime['.htm']
				});		
				res.write('<html><body><h1>404 - Not found</h1></body></html>');										
				res.end();
			} else {
				if(lstat.isFile()){
					
					console.log('>> file '+path);
					
					let buffer 	= req.url.replace(/[^a-z0-9]+$/, '');
					let pos 	= buffer.lastIndexOf('.');
					if(pos>=0){
						let ext = buffer.substring(pos, buffer.length).toLowerCase();
						if(typeof Mime[ext]!='undefined'){
							Filesystem.readFile(path, 'utf8', (err, data) => {
								if(!err){
									res.writeHead(200, {
										'Content-Length': lstat.size,
										'Content-Type'	: Mime[ext]
									});
									res.write(data);
									res.end();
								} else {
									res.writeHead(404, {
										'Content-Length': 78,
										'Content-Type'	: Mime['.htm']
									});		
									res.write('<html><body><h1>404 - Not found</h1></body></html>');										
									res.end();
								}
							});
						} else {	
							console.log('extension unknown');
							res.writeHead(404, {
								'Content-Length': 78,
								'Content-Type'	: Mime['.htm']
							});		
							res.write('<html><body><h1>404 - Not found</h1></body></html>');										
							res.end();
						}
					}					
				} else {
					if(lstat.isDirectory()){
						Filesystem.readdir(path, function(err, files){
							files = files.map(function(name){
								let lstat = Filesystem.lstatSync(DIR_WWW+name.replace('/', DS));
								if(lstat.isFile()){
									return {
										type: 'file',
										name: name,
										url : req.url+name
									}
								}
								if(lstat.isDirectory()){
									return {
										type: 'dir',
										name: name,
										url : req.url+name+'/'
									}
								}
							});
							let tmp = `<doctype>
	<html>
		<head>
			<style>
				body{ position: absolute; top: 0px; left: 0px; dislay: block: height: 100%; width: 100%; background-color: #f0f0f0; padding: 0px; margin: 0px; }
				body ul{ display: block; width: 100%; padding: 20px; margin: 0px; }
				body ul li{ position: relative; display: block; width: 100%; padding: 1px 0px 1px 20px; }
				body ul li.dir:before{ content: ''; position: absolute; top: 5px; left: 0px; display: inline-block; height: 10px; width: 15px; border: solid 1px #888888; background-color: yellow; }
				body ul li.file:before{ content: ''; position: absolute; top: 2px; left: 3px; display: inline-block; height: 15px; width: 10px; border: solid 1px #888888; background-color: white; }
			</style>
		</head>
		<body>
			<ul>
				<li class="dir"><a href="`+localUrl+`">[root]</a></li>`;
	files.forEach(function(file){
		if(file.type=='dir'){
			tmp += '			<li class="'+file.type+'"><a href="'+localUrl+'/'+file.name.replace(/^[\\]+/, '')+'">'+file.name+'</a></li>'+"\r\n";
		}
	});
	files.forEach(function(file){
		if(file.type=='file'){
			tmp += '			<li class="'+file.type+'"><a href="'+localUrl+'/'+file.name.replace(/^[\\]+/, '')+'">'+file.name+'</a></li>'+"\r\n";
		}
	});
		tmp += `</ul>
		</body>
	</html>`;
							res.writeHead(200, {
								'Content-Length': tmp.length,
								'Content-Type'	: Mime['.htm']
							});		
							res.write(tmp);										
							res.end();				
						});
					} else {
						res.writeHead(404, {
							'Content-Length': 103,
							'Content-Type'	: Mime['.htm']
						});		
						res.write('<html><body><h1>404 - Nout found</h1></body></html>');										
						res.end();
					}
				}
			}
		});
	}).listen(port, function(){
        console.log('<HttpServer:http> is now listening port '+port);
    });
	
})(8081);