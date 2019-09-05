<?php
    class Pass
        {
            private $workFolder = null;
            private $ID = null;
            var $content = null;
            var $passBundleFile = null;

            private function copySourceFolderFilesToWorkFolder($path) {
                //recurse over contents and copy files
                $dir = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($dir,RecursiveIteratorIterator::SELF_FIRST);
               
                foreach($files as $name => $fileObject){
                if (is_file($name) &&
                substr($fileObject->getFileName(), 0, 1)!=".") {
                copy($name,
                $this->workFolder."/".str_replace($path."/", "",$name));
                } else if (is_dir($name)) {
                mkdir($this->workFolder."/".
                str_replace($path."/", "",$name));
                }
                }
               }
            //make new instance from a source folder
            function __construct($path) {
                assert(file_exists($path."/pass.json"));
            
                $this->ID = uniqid();
            
                $this->workFolder = sys_get_temp_dir()."/".$this->ID;
                mkdir($this->workFolder);

                assert(file_exists($this->workFolder));
            
                $this->copySourceFolderFilesToWorkFolder($path);
            
                $this->readPassFromJSONFile($this->workFolder."/pass.json");
            }

            //import a json file into the object
            function readPassFromJSONFile($filePath){
                //read the json file and decode to an object
                $this->content = json_decode(file_get_contents($filePath),true);
                
            }
            function writePassJSONFile()
            {
                file_put_contents($this->workFolder."/pass.json",
                json_encode($this->content));
            }
            //delete all auto-generated files in the temp folder
            function cleanup(){
                
                //recurse over contents and delete files
                $dir = new RecursiveDirectoryIterator($this->workFolder, RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($dir,RecursiveIteratorIterator::CHILD_FIRST);
                
                foreach($files as $name => $fileObject){
                    if (is_file($name)) {
                        unlink($name);
                    } else if (is_dir($name)) {
                        rmdir($name);
                    }   
                }

                rmdir($this->workFolder);
                
            }
            //cleanup the temp folder on object destruction
            
            function __destruct() {
                //$this->cleanup();
            }

            function writeRecursiveManifest()
                {
                //create empty manifest
                $manifest = new ArrayObject();

                //recurse over contents and build the manifest
                $dir = new RecursiveDirectoryIterator($this->workFolder, RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($dir,RecursiveIteratorIterator::SELF_FIRST);
                
                // $files = new RecursiveIteratorIterator(
                // new RecursiveDirectoryIterator($this->workFolder),
                // RecursiveIteratorIterator::SELF_FIRST);

                foreach($files as $name => $fileObject){
                if (is_file($name) &&
                substr($fileObject->getFileName(), 0, 1)!=".") {
                $relativeName = str_replace($this->workFolder.
                "/","",$name);

                $sha1 = sha1(file_get_contents(
                $fileObject->getRealPath()
                ));
                $manifest[$relativeName] = $sha1;
                }
                }

                //write the manifest file
                file_put_contents($this->workFolder."/manifest.json",
                json_encode($manifest));
                
                }
            //generate the bundle signature
            function writeSignatureWithKeysPathAndPassword($keyPath, $pass)
                {
                $keyPath = realpath($keyPath);

                if (!file_exists($keyPath.'/WWDR.pem'))
                die("Save the WWDR certificate as $keyPath/WWDR.pem");
                
                if (!file_exists($keyPath.'/passcertificate.pem'))
                die("Save the pass certificate as
                $keyPath/passcertificate.pem");

                if (!file_exists($keyPath.'/passkey.pem'))
                die("Save the pass certificate key as
                $keyPath/passkey.pem");
                $output = shell_exec("openssl smime -binary -sign".
                " -certfile '".$keyPath."/WWDR.pem'".
                " -signer '".$keyPath."/passcertificate.pem'".
                " -inkey '".$keyPath."/passkey.pem'".
                " -in '".$this->workFolder."/manifest.json'".
                " -out '".$this->workFolder."/signature'".
                " -outform DER -passin pass:'$pass'");
                //print(file_get_contents($this->workFolder."/signature"));
                } 
                
                //create the zip bundle from the pass files
                function writePassBundle()
                    {
                    //1 generate the name for the .pkpass file
                    $passFile = $this->workFolder."/".$this->ID.".pkpass";
                    print_r($passFile);
                    //2 create Zip class instance
                    $zip = new ZipArchive();
                    $success = $zip->open($passFile, ZipArchive::CREATE | ZIPARCHIVE::OVERWRITE);
                    if ($success!==TRUE) die("Can't create file $passFile");
                    debug_backtrace();
                    //3 recurse over contents and build the list
                    // $files = new RecursiveIteratorIterator(
                    // new RecursiveDirectoryIterator($this->workFolder),
                    // RecursiveIteratorIterator::SELF_FIRST);
                    $dir = new RecursiveDirectoryIterator($this->workFolder, RecursiveDirectoryIterator::SKIP_DOTS);
                    $files = new RecursiveIteratorIterator($dir,RecursiveIteratorIterator::SELF_FIRST);
                    
                    //4 add files to the archive
                    foreach($files as $name => $fileObject){
                    if (is_file($name) &&
                    substr($fileObject->getFileName(), 0, 1)!=".") {

                    $relativeName = str_replace($this->workFolder."/",
                    "",$name);
                    $zip->addFile($fileObject->getRealPath(), $relativeName);
                    }
                    }
                    //5 close the zip file
                    $zip->close();

                    //6 save the .pkpass file path and return it too
                    $this->passBundleFile = $passFile;
                    return $passFile;
                    }
        }
?>