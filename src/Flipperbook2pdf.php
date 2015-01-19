<?php

namespace Fb2pdf;

use Fb2pdf\Tools\Cli;

class Flipperbook2pdf
{
    private $url;

    private $output;

    private $tmpPath;

    private $outputPath;

    public function __construct($url)
    {
        $this->url = $url;
        $this->tmpPath = sys_get_temp_dir() . '/'. uniqid() . '/';
        $this->output = 'output.pdf';
        $this->outputPath = $this->tmpPath . $this->output;
    }

    public function run()
    {
        // Create tmp dir
        if ( ! mkdir($this->tmpPath, 0777, true)) {
            return Cli::finish('Temp path is no writable (' . $this->tmpPath . ')', 'error');
        }

        // Download all the pages
        Cli::output('Downloading pages... ', 'notice');
        $page = 1;
        do {
            $result = $this->downloadPage($page);
            $page ++;
        } while ($result);

        // Convert pages to PNG
        Cli::output('Converting pages... ', 'notice');
        $this->convert2PNG();

        // Convert pages to PNG
        Cli::output('Creating PDF... ', 'notice');
        $this->convert2PDF();
        Cli::output('Done!', 'success');

        return $this->outputPath;
    }

    /**
     * Download SWF page
     *
     * @param  integer $page
     * @return bool
     */
    private function downloadPage($page)
    {
        $page = str_pad($page, 4, "0", STR_PAD_LEFT);
        $url = $this->url . '/files/assets/flash/pages/page' . $page . '.swf';

        // cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code == 200) {
            Cli::output('Page ' . (int) $page . ' downloaded!', 'success');

            file_put_contents($this->tmpPath . $page . '.swf', $result);

            return true;
        }
    }

    /**
     * Convert pages to PNG
     *
     * @return bool
     */
    private function convert2PNG()
    {
        $files = glob($this->tmpPath . '/*.swf', GLOB_BRACE);
        foreach ($files as $i => $file) {
            exec('swfrender '.$file.' -o '.$this->tmpPath . basename($file, '.swf') . '.png');
            Cli::output('Page ' . (int) ($i + 1) . ' converted!', 'success');
        }
    }

    /**
     * Convert PNG pages to PDF
     *
     * @return bool
     */
    private function convert2PDF()
    {
        return exec('convert ' . $this->tmpPath . '*.png '.$this->outputPath);
    }
}
