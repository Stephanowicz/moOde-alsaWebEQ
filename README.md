# moOde-alsaWebEQ
simple web gui for alsaequal for use in a moOde setup

![image](https://user-images.githubusercontent.com/12005507/130596179-46a1365a-0a7a-4a8f-920d-149073aab0e5.png)


This is a rework based on the <a href="https://github.com/elovattibr/webmixer" target="_blank">webmixer from elovattibr</a>

I removed all the stuff except the EQ frontend. The classes and the standalone webserver also have been removed.  
I think on a moOde installation all necessary dependencies already have been installed (e.g. alsa-utils, alsaequal, alsa-tools)  
It is expected that the name of the device is "alsaequal" (there should be a file "alsaequal.conf" in /etc/alsa/conf.d/)  

You have to put the files in a (new) folder in the /var/www/ folder (e.g. /var/www/alsamixer)  
Then You may access the page with Your moOde webaddress adding /alsamixer --> moode.local/alsamixer  

This is still in testing - the frontend is not yet integrated into the moOde GUI  
You can access the stored curve presets fom the moOde db - you may save, rename, delete and create curve presets  
You may instantly switch the output source for mpd - for the EQ You have to select "ALSA graphic eq" in the combobox  


Have fun!

Cheers, Stephanowicz
