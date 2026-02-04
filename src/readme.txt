#Author Riccardo Castagna
#email: 3315954155@libero.it 
#cUrl Multi fast & furious and also very simple and stable.

The cUrl_extension is one of the most important extensions of the PHP especially when we have to interface with external APIs and we must communicate with other applications.

Well, as we know, many novels and many volumes have been written on this topic :))) :
something about to sleep while running and to stay calm and others nice and beautiful things! I joke ... 
 
During a project that I was carrying out, where I needed to make multiple and simultaneous asynchronous requests, I came across a problem with the cUrl_Multi, which, after some vain but useful research, some analysis and many tests I developed a solution through an insight.

The problem is when you use the cUrl Multi with arrays to add the handles because it can cause the loss of some requests.
 
Well, sometime the solutions are complex and sometime are simple.

In this case, the cUrl Multi, adding a simple solution during the execution of the first loop to add the handles, became stable.   

The problem has been in how the array with the cURL handles was executed, 
in fact if the loop of the array is executed normally all together, from the key number zero to the key number (n)keys, the cUrl multi, sometime, could returns errors or loses some hits.

The solution, I found, is to detach the first key adding the first handle to it without a loop, than executing the first loop, to add the handles, for the subsequent keys. 
All the requests will still remain anyway simultaneous because they are performed by the second loop with the curl_multi_exec.

In this way is very stable, light and fast. I have stressed very much this class with several tests, and it never lost a beat.

Since I had read somewhere, I do not remember where, that cUrl_Multi gave some problems when the number of requests were equal or greater than 10,
I did also a test with 13 simultaneous requests and it is ok, no problems.
 
I have also simulated an error inserting a wrong url inside the array and it go on to execute all the rest with no stop and no errors.    


About this topic I wrote also something here:
"3315954155 at libero dot it" 
http://php.net/manual/it/function.curl-multi-add-handle.php#122964

As default options inside the main class there are:

curl_setopt($x, CURLOPT_URL, $y); 
curl_setopt($x, CURLOPT_HEADER, 0); 
curl_setopt($x, CURLOPT_FOLLOWLOCATION, 1); 
curl_setopt($x, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($x, CURLOPT_TCP_FASTOPEN, 1); 
curl_setopt($x, CURLOPT_ENCODING, "gzip,deflate"); 
curl_setopt($x, CURLOPT_SSL_VERIFYPEER, 0); 
curl_setopt($x, CURLOPT_SSL_VERIFYHOST, 0);

obviously you can change this options, according to your needs and according
to your libcurl version (view: https://curl.haxx.se/libcurl/c/symbols-in-versions.html) 
editing the main class file: ./lib/class.curlmulti.php  
private function set_option($x, $y)  
--------------------------------------------------------------------------------------------
Usage:
 
include_once("./lib/class.curlmulti.php"); 
$ref= new cURmultiStable;

$urllinkarray = array('http://php.net/manual/it/function.curl-multi-add-handle.php', 
'http://php.net/manual/en/function.curl-multi-init.php', 
'http://php.net/manual/en/function.curl-multi-setopt.php'
);

$urls = $ref->runmulticurl($urllinkarray);

foreach ($urls as $value){
echo $value; 
}
----------------------------------------------------------------------------------------------

OR:

include_once("./lib/class.curlmulti.php"); 
$ref= new cURmultiStable;

$urllinkarray = array('http://php.net/manual/it/function.curl-multi-add-handle.php', 
'http://php.net/manual/en/function.curl-multi-init.php', 
'http://php.net/manual/en/function.curl-multi-setopt.php'
);

$urls = $ref->runmulticurl($urllinkarray);

echo $urls[0],$urls[1],$urls[2];  
---------------------------------------------------------------------------------------------- 
OR:

include_once("./lib/class.curlmulti.php"); 
$ref= new cURmultiStable;

$urls = $ref->runmulticurl(array('http://php.net/manual/it/function.curl-multi-add-handle.php', 
'http://php.net/manual/en/function.curl-multi-init.php', 
'http://php.net/manual/en/function.curl-multi-setopt.php'
));

echo $urls[0],$urls[1],$urls[2];
----------------------------------------------------------------------------------------------
OR for a single request:

include_once("./lib/class.curlmulti.php"); 
$ref= new cURmultiStable;

$urls = $ref->runmulticurl(array('http://php.net/manual/it/function.curl-multi-add-handle.php#122964'));

echo $urls[0]; 
//or 
foreach ($urls as $value){
echo $value; 
}
---------------------------------------------------------------------------------------------- 
THE END ... SIMPLE, FAST, LIGHT AND STABLE !!! 

Sincerely, good job to everybody.  