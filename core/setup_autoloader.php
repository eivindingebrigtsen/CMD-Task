<?php

class SetupAutoloader {
    private $basepath;

    public function __construct() {
        $this->basepath = __dir__ . '/../';
    }

    public function run() {
        $dir_iterator = new RecursiveDirectoryIterator($this->basepath);
        $files = array();

        foreach(new RecursiveIteratorIterator($dir_iterator) as $entry) {
            $file = str_replace((__dir__.'/'), null, $entry);

            // Only allow .php and .inc files
            if(substr($file, -4) == '.php' || substr($file, -4) == '.inc') {
                if(strpos($file, './core/') == false) {
                    array_push($files, $file);
                }
            }
        }

        $this->renderAutoLoader($files);
    }

    private function renderAutoLoader($files) {
        if(is_array($files) && empty($files) === false) {
            $outputfile = __dir__ . '/autoloader.php';
            $file_handler = fopen($outputfile, 'w');

            if($file_handler) {
                fwrite($file_handler, "<?php\n\n");
                $generated = "// Automatically generated @ " . time('Y-m-d H:i:s') . "\n";
                fwrite($file_handler, $generated);

                fwrite($file_handler, "function __autoload(\$class_name) {");

                foreach($files as $file) {
                    $output = "\n\trequire_once('" . $file . "');";
                    fwrite($file_handler, $output);
                }

                fwrite($file_handler, "\n}\n\n?>");
            } else {
                throw new Exception('System Error: Could not write autoloader file');
            }

            fclose(($file_handler));

        } else {
            throw new Exception('System Error: no sources available to autoload');
        }
    }
    

}

?>
