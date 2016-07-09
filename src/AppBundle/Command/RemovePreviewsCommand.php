<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RemovePreviewsCommand extends ContainerAwareCommand
{
    const WEEK = 604800; // 7days * 24 * 60 * 60

    protected function configure()
    {
        $this
            ->setName('feedback:previews')
            ->setDescription('Removes old unused previews')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $rootDir = $container->getParameter("kernel.root_dir");
        $previewsDir = $rootDir."/../web/previews";
        $dir = dir($previewsDir);
        $now = new \DateTime();
        $deleted = 0;

        while (false !== ($entry = $dir->read())) {
            $filePath = $previewsDir."/".$entry;
            $mimeType = mime_content_type($filePath);

            if (!self::startsWith($mimeType, "image") || !is_writable($filePath)) {
                continue;
            }

            $diff = $now->getTimestamp() - filemtime($filePath);

            if ($diff >= self::WEEK) {
                echo $entry." :: ".$diff."\n";

                unlink($filePath);
                $deleted++;
            }
        }

        $dir->close();
        $output->writeln(sprintf("%d file has been deleted", $deleted));
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    protected static function startsWith($haystack, $needle)
    {
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
}
