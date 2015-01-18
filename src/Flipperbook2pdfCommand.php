<?php

namespace Fb2pdf;

use Symfony\COmponent\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Flipperbook2pdfCommand extends Command
{
    public function configure()
    {
        $this->setName('grab')
            ->setDescription('Download all the pages and convert to PDF.')
            ->addArgument('url', InputArgument::REQUIRED, 'URL')
            ->addOption('output', null, InputOption::VALUE_OPTIONAL, 'Output file name', 'output.pdf');
    }

    public function execute(InputInterface $input)
    {
        $flipperbook2pdf = new Flipperbook2pdf($input->getArgument('url'));

        $output = $flipperbook2pdf->run();
        if ($output) {
            rename($output, $input->getOption('output'));
        }
    }
}
