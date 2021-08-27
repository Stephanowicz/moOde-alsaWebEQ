# moOde-alsaWebEQ
simple web gui for alsaequal for use in a moOde setup

normal view when called directly in the browser:  
![image](https://user-images.githubusercontent.com/12005507/130596179-46a1365a-0a7a-4a8f-920d-149073aab0e5.png)
integration in moOde:  
![image](https://user-images.githubusercontent.com/12005507/131179713-252bb026-ace2-4a63-9ce8-5eec915a0167.png)


This is a rework based on the <a href="https://github.com/elovattibr/webmixer" target="_blank">webmixer from elovattibr</a>

I removed all the stuff except the EQ frontend. The classes and the standalone webserver also have been removed.  
I think on a moOde installation all necessary dependencies already have been installed (e.g. alsa-utils, alsaequal, alsa-tools)  
It is expected that the name of the device is "alsaequal" (there should be a file "alsaequal.conf" in /etc/alsa/conf.d/)  

You have to put the files in a (new) folder in the /var/www/ folder (e.g. /var/www/alsamixer)  
Then You may access the page with Your moOde webaddress adding /alsamixer --> moode.local/alsamixer  

This is still in testing
You can access the stored curve presets fom the moOde db - you may save, rename, delete and create curve presets
(the curve presets of this app have stereo values - the will be stored and restored as stereo values - **be aware that the moOde presets only have one value per channel...  
so the already integrated equalizer in moOde will load the 'wrong' values if they are in stereo**)
You may instantly switch the output source for mpd - for the EQ You have to select "ALSA graphic eq" in the combobox  
the frontend can now be integrated into the moOde GUI: 
  
**footer.php** (needs to be minified - e.g. with https://minifycode.com/html-minifier/ )  
--> add the divs for the modal around line 130 (search for ```<!-- POWER -->``` and put it right before)  
```
<!-- ALSAGraphicsEQ -->
<div id="ALSAGraphicsEQ-modal" class="hide" tabindex="-1" role="dialog" aria-labelledby="ALSAGraphicsEQ-modal-label" aria-hidden="true" style="transform: translate(-50%);
    border-radius: 6px;
    left: 50%;
    position: fixed;
    z-index: 10001;
    box-shadow: 0 3px 7px rgba(0,0,0,.3);">
	<div class="modal-header">
		<button aria-label="Close" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="ALSAGraphicsEQ-modal-label">ALSA GraphicsEQ</h3>
	</div>
	<div class="modal-body" style="max-height:80vh;">
		<div id="ALSAGraphicsEQ" style="max-height:80vh;"></div>
	</div>
	<div class="modal-footer">
		<button aria-label="Close" class="btn singleton" data-dismiss="modal" aria-hidden="true">Close</button>
	</div>
</div>  
```  
  
**templates/indextpl.html**  --> adding menue item - search for ```context-menu-playback``` and put it somewhere there  
```
<li><a href="#notarget" data-cmd="ALSAgraphicEQ"><i class="fal fa-sliders fa-rotate-90 sx"></i>Graphic EQ</a></li>
```  
  
**js/scripts-library** (this also needs to minified... but You will need gulp or smth like that as these files will be combined together...sry)  
--> menue function call (search for ```// Playback ellipsis context menu```  
```	
else if ($(this).data('cmd') == 'ALSAgraphicEQ') {
		ALSAgraphicEQ();
	}
```  

--> the function itself - put it at the end of the file  
```
function ALSAgraphicEQ() {

	$('#ALSAGraphicsEQ').empty();
	$('#ALSAGraphicsEQ').load('./alsamixer/ALSAGraphicsEQ.html');
	$('#ALSAGraphicsEQ-modal').modal();
}
```  



Have fun!

Cheers, Stephanowicz
