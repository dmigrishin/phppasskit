<?php
    class Pass
        {
            private $workFolder = null;
            private $ID = null;
            var $content = null;
            var $passBundleFile = null;

            private function copySourceFolderFilesToWorkFolder($path) {
                //recurse over contents and copy files
                $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path),
                RecursiveIteratorIterator::SELF_FIRST);
               
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

            //delete all auto-generated files in the temp folder
            function cleanup(){
                //recurse over contents and delete files
                $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->workFolder),
                RecursiveIteratorIterator::CHILD_FIRST);

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
                $this->cleanup();
            }
            
        }
?>