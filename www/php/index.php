<?php
	session_start();
	if(isset($_GET['path'])){
		$_SESSION['dir'] = $_GET['path'];
		header('Location: index.php');
		exit();
	} else {
		if(!isset($_SESSION['dir'])){ $_SESSION['dir'] = ""; }
		if(!is_dir($_SESSION['dir'])){ $_SESSION['dir'] = '.'; }
	}
?>
<html>
	<head>
		<meta name="viewport" content="width=device-width, user-scalable=no">
		<style>
			body{
				font-family: Arial;
				font-size: 12px;
			}
			.bar{
				display: block;
				padding-left: 20px;
				background-position: 0px 0px;
				background-repeat: no-repeat;
				height: 16px;
				line-height: 16px;
				margin-bottom: 1px;
				color: #333333;
				border: solid 1px #333333;
			}
			.bar a{
				text-decoration: none;
				color: black;
			}
			.bar a:hover{
				color: #000080;
				font-weight: bold;
			}
			.item{
				display: block;
				padding-left: 20px;
				background-position: 0px 0px;
				background-repeat: no-repeat;
				height: 16px;
				line-height: 16px;
				margin-bottom: 1px;
				color: #333333;
				text-decoration: none;
			}
			.item:hover{
				background-color: #F0F0F0;
			}
			.dir{			background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABpElEQVQ4jaWTQUhUQRyHRxERJAgCCTZJJEmERWS9dfW4dO3a7Yn4nkgQiCI8p0WSZ+IisiMiCxIUgQRbJKIkwuLIvpuyJ9nxtBcPXWJet6/DKJEr64I/+GBgZj6YH/MX3DPiehGrUSqFDCfrIxyvDWOMwVrbmiBWow0bRytDaK3vlIh/l9UN4GBpgL3Ffn68e8r3hV5KYYqv848xxpAkiRNUChlIVuH3h0Zuyc5cD1EUUavVnOBkfQQuc1APKefTt3K4PMj++2fs5vr4Fj6hFKYohSmMMYjjtWG4mKWcT99ZGAD1EIDt6W6iKEKU82k4m7oq8mYPjfwpvwYUxaCLIAgQRytDEHtUChl3KFltyq/9V4Bia7IT3/cRh8uDEHtXT1CujybUSy8BxeZEhxMcLA1A7PEzeu4EF7NNOf88Big2xtudYG+xH2KP3VyfE5xNNeW0+AJQKK/NdWCM+e+T7Mz18GXmEZ/ePuTjmwdsT3dTDLrYmuxkc6KDjfF2lNdGNptFSomw1qK1RkqJ7/stI6VEa+1mwVqLMYZqtdoy18MmuGf+AkKkQlLzp77LAAAAAElFTkSuQmCC); }
			.file{			background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAA70lEQVQ4jZXTwYqEIBzH8R4/eozmGDqGYYmu7Vy2ew8Q9AQdBhHiX789zTLONGwKXxDRDyKY9bcbnnPuG9Z+RXPVdQgh4Ghkru+jhX3fsW0b1nVFCAH3+x1lecEwDPDeHwDOAQCI6DDvPaRssCwLfobh7SaZte7j4UeMX3EVNcryAqXaGDDG/gsQEaZpwjiOMMbGgNbmFEBEmOcZWpsYaDt9GiAitJ2OAaXaJODtDWSjkgDZqBdANmmAbGJA1DIJELWMAS7qJICLOgYYF0kA4yIGKsaTgIrxF6BiAD7/hecA4LH/D8iLAnmen68oIuAXpEm+x0CgNpYAAAAASUVORK5CYII=); }
			.parent{		background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAPUlEQVQ4jWNgoCXYzsDwfzsDw3+KNJNlCLpmkgzBpZkoQwhpxmsIsZqxGkKqZgxDKDaAaoE4agCVDCAVAABedsnlW5ImOAAAAABJRU5ErkJggg==); }
			.script{		background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAg0lEQVQ4jZ2SUQ7AIAhDe/QejZuxjwkDg4zNpB8YeRQQJPWvAChIanWqhHhE5Ay477AipIRXgMUkXPbYbB8BGZYfkkwuWkCsDmS1LUx6H84A5QxGgKmT1kH8KOZih7RDrNamih7wgODJsY1RC7ZrEfHqFo+HuO99338JsCpfteB1xakuVq/AlNJXB20AAAAASUVORK5CYII=); }
			.image{			background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACVklEQVQ4jXXMzUsTABjHcVd0EAL/CaGXs3XrlOA16BJ6ERzWodDIBWkqVjRTMqLpWpnYm+8v5et0DJ3TtdFeyL3o2HS+r+nYq85tuX07SLO19cD39PD75ACIe51Za+xf5clHG+JeJ3vBGNku5w/w7yWTEE9A5Nfxv1YRQih6Tt8XBWubbqKx+P+Bv8eBKBR2HVLYdcjVtm3qxFJUC0a8vmA6EI/Hs453D0BvPa5v9ID6Z2NIO/pYsrvSgWg0CgWCVJqaGtQ7sOqHjSA4fWDbg2ndPnerm5jTGNKBSCSSGscKznDljhKBDAQyaDKC2QOGdVAawpTeesCkQp0OhMP7KaC0UEJ+pTUFCGQw5UyiW4NpfZjisirG5LPpgC8QggIB/kt5FBUNZABPNUnmV2BSF8oEHn+w4vEGMH+zYNPbOdscJFcSIq97IwXIvh8x64BxbRagod3ElsfHVghcfhCbYtwIXON27DKn38a43h9hdDmBwg5fF4KZQG2rFteOF5cf7F7wLQmJG06xbcnFqcunXXOA3AETNhhWBzKB6hdzODZ2sXthZVNDQH8Rt+Ycbs15PNoLzKgbGLLBiAX6VVmA+40KrK6fmD1gdINyJcGk/YjRpSOGrQkGrEl6LTC0CD0z/kzg3qNxfjh2MLpBuwVz66BcBbkDRpZh0AbdZugxwWeljxKhiPEp1QlQ8XCId4N23vTYeN1lRvppEdl7PdIOHW3tGlpl87xqU9H8coLmFjk3K+uZVi6cAHqThRZJJ+UVdZQIRRSXVWWtRCiivKKOFkknepMFgN8WVEDiY5/CyQAAAABJRU5ErkJggg==); }
			.web{			background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACOUlEQVQ4jZXQbUgTcQDH8b2LWi+U7OF1vYjIoDdSUeCbHqjAwh4I7AGysDCECoJeVghjGUiUb5LMM6iUPbUxN7Zu2YODyGTTyDKnXqdONzSQ6263028v4s6WQvWHHxx3Xz7cnS3W1cXvE8WXRKMvCq6DoRCqqrLUsYmxWMGN+fl55ubm0HUdVVWZnZ1FENqIx+MoirIEIIoAGIZhLZ/Po+s6mqahKAp+f4CZmRm64/FFb2KLRn8Buq4vmgm43B48Xh+C0EYw2FkIRCJRADRNQ9M03vamaIkNEB6cJJCQmcxMk8lkSKVSJJNJzN4CwuEIAOrkIA/uNnHwSiuvxiQGgEfyD249djOR8JFOpxkdHcXsLaAzFAZgytNAf9Ua3h1Zy3PhNq1T36n7lKNaCDMcOcr0t26y2SxmbwHmN6XvHCCxezlDp1fx7GYtFe1D7GpKUnqxlZR4iGy8EUVRFv8DfyAIwHBtKT377DwsX8/G62G23HhNScU9yiovo3Tv4KOrjlwuh9kvAP4AAB/OlNNzuIi+ypW0nCjDebYC+/Z6LpzaQ/5pCRNiI4ZhYPYW4PX5AUg238dTamfo/Grcxzawc/NeqrZtZbB+HX3Xihj//B7DMDB7C3B7fQDIskyoppr2TSvoP16MdM7OeM0yUleLGRMbkGWZfD6P2VuAy+0FQJIkJEmiV2jmTfV+EpfK+dp4ki+hJ9YzALO3gA6XG4CRkZG/DsDsF4AOF/9z/uxtDqcTh8Px73M6C4CfpCVgX7zqTXYAAAAASUVORK5CYII=); }
			.cfg{			background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAADE0lEQVQ4jXXTT2yTdRzH8V3xACdNjPHAzaAxHjyIMYQdJiQSIYsGRCIVAziXCVHB4QgMNoOTBcNkdMxhKHNlqa7C1nWdUCZmW/evtEI7Vppue7Zu3do+40fbpw/P0z9vDhIePfi9fr95Xb6fTwnApK+N5HKA0T9ric70c6tnO5PeHxh0mvBc34fL/gH/NyWBQJTTRz6js+F1zn17il7zOr6qasfeXInlfAcu6zFcbVuoP9nCwK1hEskV8vmCAfT1BfFNJpnoqcTnjzM12oXLcZtUMkzE/wd5XWVlaRZLh50fW9qR5hbRNN0AHL13iQqVaaETkTVmZQ1J1tDzxadH8eQDvqyupa6hmUAwRDqToVj4Z1/S47jDnFBJAVkgoeSIp3PkCkW0+atI3kvU1jdQsd+Er6MMr7sVSZJQs4oBzAiVKaEzKWvclzWmZQ0tX0TqLEUMfYLNZmP/R5sIWV/A1bIJR7edhei8AUSEShJ4AGSARwWdnP8Qyl/lPPTsxWazsWdHGWNnV/P3z88Q7NqIx+MxgPtCxS0p9IXTDExniAgdW9WzpG5uZeV6Gb5f38N24jWGT69m8MwqLNXrqKurN4ApodIyIvO9exlnKEV38CH33BZSN7cRvbqBpd43CF95iVDHi4y3vcqHpW/x9eEaAwgIlX5JoSecpj+SYSKmMuwPYjGt4UrVRpadb2L9fC2tO57npwOvsGXrTsxN5w3gjlDxpXR8QsebfETvVApvTOXagJ+IrNH87nNs3/Ayb68vZdfmdzj0xRHGR8YNwC9UPELHI2vcTemE0jnihSICUAANaDJf5mNTBRfOteLqdrAYXTAAr1AZEjqDskYkX2QBiAMr8DQf5otWjp5oZHRkgtjCIlnlXzkYEypBIAREgDlgEUg8eW0aaDK309h0kXB4hmxWheKTJLrdAaSlJAklw+xSHPfQGFa7k7MXfuFgzXfs/rSazeV72VNZQ2eXk1gsQS6XM7owNx/jkvUapopvKN9Vxfu7D7LvwHEOH2vk1JlW2i7/xu+OG4z7AsxHY2SU7H/q/BjlJU0YiopoUAAAAABJRU5ErkJggg==); }
			.del{			background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABCklEQVQ4jZWTTYqEMBCFc3zxGPZSTEciUdGJ05tx7wEET+CiCQEpfbOymfKH0cCDpKj3pVJJRPN64a+s/UZdf7G5KQp473E0hG0aFliWBfM8Y5omeO/xfr8RRQ+0bQvn3AHAWgAAER3KOQetM4zjiJ+23VUi6tqemlcl8omnShFFDxiTc0BV1f8CiAh936PrOlRVzQFlWV0CEBGGYUBZVhyQF+UnAUIAQjDTNpYXJQcYk++SV8N2TUT7HujMHO54ZCYi6MxsADrbnfXMTETQOuMAlepbFahUc4BU6a0eSJVyQCLVrVtIpOKAOJGX3wERIU7kBhAnAM7/AqsGwJr/AQRhiCAIrisMGeAXhSN+MqXaAv4AAAAASUVORK5CYII=); }
			.alert{			background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABBklEQVQ4jZXTQYqEMBAFUI8vHsNeStKRSDQko9Obce8BBE/gogkBif5Z2Vh2pGeED6FIvdKoSfd44Ji2/Ya1X2Stmgbee8SupO06Uti2Deu6YlkWeO/xfD6R5zf0fQ/nXARoWwBACCEa5xykrDDPM376/u1OEmvbaCOS5LVm/I67KJHnNyhVU8AY+xEIIWAcRwzDAGMsBbQ20eY9x/o0TdDaUKBu9OX0MxBCQN1oCihVR6ef13vezkBW6l+ArNQJkNXls8fOQsqKAqKUUeCcfY8oJQW4KKPNVzUuSgowLj5OPwKMCwoUjF9+xrEUjJ+AggG4/hfIGwKw738BaZYhTdO/J8sI8AutG2a2n9yNfQAAAABJRU5ErkJggg==); }
			.zip{			background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAc0lEQVQ4ja2TQQ7AIAgE+f8rOPgQHtJHcPewHipNTUSsdJONiYHJBpSQFP0KICLXIeApqgpUhVwCkdvMxYUMAM/MxYWMN1WnsQ2yBvRmgIB+WsMewObw8hkgk8CKj2aQ3kL6HUQpPC3/QtQ8BaxSbG3hqxqIyxhH0TFX/wAAAABJRU5ErkJggg==); }
			.snd{			background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACkElEQVQ4jY3SbyzUARzHcfegtZ60tbXajCmTe6CNqazaetIDWw9qY2VCw8jEqB8WY4emxcW4HTcrdpskYUbGiMji/DkkRE1+bkTrnH+j32Fz9+6BuqOy9dk++34fvZ58vw78SnbV1D+bUzPNw/IJkjIbEEURSZLYHYffi+adhMXKvknKbEAo70On0+1BbIC6fRWLlX0bE1tChx4ua+b2IDagoOk7FitsbcPm9s7cvcfEltiqeFCDTqfbC6haFlguVbFcqqKoLYKKjpc871+kfx7Wt2B9c2d26CE1vRKlUmkHzBVqtq6fBl9X8HUlsVNGZJmMewXeFL8e5sU4mMywKEG7HpJSyhEEYQfYqC5m218OmaFIJUrEhhyqJ4NIbz9MiFpGULIzj5tF6idhbg1a9XA3QbsLCPQERTA/6kpQqdVMTM2yatpkbKYdRe0p/FJkRKWFk9xp5csSNA9CdEyxHdi+chwK72NeXyI+Ph5BENBqtayYzLQMFxKcdZRLgSeIaYMxIzQOQniEyg7IFQZCm7ZsgLu7OzKZjIiIO6yYzBi73OitdCT6jZXBeagfguCQXDtwIHXVBvicO4uTkxPpGRl8mvwKFiNrAxforXQkstVK9yzUDkFAQLYdcA4bQK4wUDe0hFarZWJ8dOeuFiOm0UiMXW4UZZ/hdquVtwaoGQJ//yw74BfXyJGrPXgLY+TWLdH3cZaNb2WYPoSx0OdD37ODCE9ekd8D+s9Q9f4PwGCY4fy1Mo5dbMTFtxsX324WejwxdZ+kp+wQSXG3iIouIjRcRUhIHoE3ldwIeGQHAAyGaRKTnyL3UiH3yicvzYO8VA9io8NISEhAEIS/qtFo9r6yJEmIosjIyMh/VRRFAH4CX3sdbdybBt0AAAAASUVORK5CYII=); }
			
			@media (max-width: 767px) { 
				body{
					font-size: 16px;
				}
				.bar{
					padding: 5px 20px;
				}
				.item{
					padding: 5px 20px;
					background-position: 0 5px;
				}
			}
		</style>
	</head>
	<body>
	<?php
		// Liste les fichiers et dossiers
		if($_SESSION['dir']==''){
			$current_path = './';
		} else {
			$current_path = $_SESSION['dir'];
		}
		if(substr($current_path, -1)!='/'){ $current_path .= '/'; }
		$i = strlen($current_path)-2;
		while($i>0 && substr($current_path, $i, 1)!='/'){ $i--; }
		$parent = substr($current_path, 0, $i+1);

		
		$pointer_dir = opendir($current_path);
		$dirs = array();
		$files = array();
		while($item = readdir($pointer_dir)){
			if($item!='.' && $item!='..'){
				if(is_dir($current_path.$item)){
					$dirs[] = $item;
				} else {
					if(is_file($current_path.$item)){
						$files[] = $item;
					}
				}
			}
		}
		closedir($pointer_dir);
		sort($dirs);
		sort($files);
		
		// Chemin d'accès
		$nodes = explode('/', '[Root]' . substr($current_path, 1, strlen($current_path)-1));
		$line = "";
		$sum = '';
		foreach($nodes as $node){
			if($node!=''){
				$sum .= ($node=='[Root]'?'.':$node) . '/';
				$line .= '<a href="index.php?path=' . $sum . '">' . $node . '</a>/';
			}
		}
		echo '<div class="bar">Chemin : ' . $line . '</div>';
		
		// Parent
		echo '<a class="item parent" href="./index.php?path=' . $parent . '">..</a>';
		
		// Dossiers
		$len = sizeof($dirs);
		for($i=0; $i<$len; $i++){
			echo '<a class="item dir" href="./index.php?path=' . $current_path . $dirs[$i] . '/">' . $dirs[$i] . '</a>';
		}
		
		// Fichiers
		$len = sizeof($files);
		for($i=0; $i<$len; $i++){
			$extension = explode('.', $files[$i]);
			$extension = strtolower($extension[sizeof($extension)-1]);
			switch($extension){
				case 'js':	
					$class='script'; 
				break;
				case 'jpg':
				case 'png':
				case 'jpeg':
				case 'jpg':
				case 'bmp':
				case 'gif':
				case 'ico':
					$class='image';
				break;
				case 'html':
				case 'htm':
				case 'php4':
				case 'php':
				case 'swf':
					$class='web';
				break;
				case 'css':
				case 'sql':
				case 'htaccess':
					$class='cfg'; 
				break;
				case 'db':
					$class='del'; 
				break;
				case 'bat':
				case 'exe':
					$class='alert'; 
				break;
				case 'zip':
				case 'rar':
				case 'jar':
				case 'gz':
					$class='zip'; 
				break;
				case 'wav':
				case 'avi':
				case 'mp3':
				case 'mp4':
				case 'ogg':
					$class='snd'; 
				break;
				default: 	
					$class='file'; 
				break;
			}
			echo '<a class="item ' . $class . '" href="./' . $current_path . $files[$i] . '">' . $files[$i] . '</a>';
		}
	?>
	</body>
</html>