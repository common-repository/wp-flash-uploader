Since version 2.6 TFU can read external language files.
This makes it possible that you can easily translate TFU 
into you language.

The languages for english, german and spanish are only delta 
files used by TFU because this 3 languages are build into the flash.
The language files for English, German and Spanish can be used to 
overwrite language strings in the flash. Copy the text you want to 
change from the default.xml to the language file.

The default.xml language file is a template you should use if
you want to translate TFU into your language.
Copy the default.xml and use your language shortcut for
your file name e.g. fi.xml. Then translate the file and
make sure to store the file as UTF-8!
You can test your language file by adding ?lang=<your language>
to the flash - e.g. twg_flash_uploader.swf?lang=fi

For missing keys always the english value is used. 
Please translate missing keys if you are able to do it ;). 
Open the default.xml and check if there are additional translations
at the bottom. It this is the case copy them to your language
file and translate them.
Please sent fully translated language files back to 
tinywebgallery@mdempfle.de. I will include them to the 
next build. To get a fitting flag please send me an e-mail.

Status of the language files:
English en:     latest  100%
German de:      latest  100%
Spanish es:     latest  100%
Hungary hu:     2.10.5  100%
Lithuania lt:   2.10.2   99%
Romania ro:     2.10.2   99%
Czech cz:       2.8.3    97%
Rusian ru:      2.9      98%
Japanese jp:    2.9      98%
Chinese cn:     2.8.3    97%
Chinese tw:     2.8.3    97%
Swedish se:     2.8.3    97%
Portuguese pt:  2.8.3    97%
Catalan ct:     2.8.3    97%
Italian it:     2.7      96%
Bulgaria bg:    2.7.4    96% 
French fr:      2.7      96%
Dutch nl:       2.7      96%
Norway no:      2.6.1   ~95%
Polish pl:      2.6.1   ~95%
Slovak sk:      2.6.1   ~95%
Brasilian br:   2.6.1   ~95%
Danish da:      2.6.1   ~95%
           
Have fun using TFU,
Michael