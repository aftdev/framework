<?php

namespace AftDev\Test\Feature\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends SymfonyCommand
{
    protected static $defaultName = 'test:command';

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Test Command Output');
        $output->writeln('ArgA: '.($input->getArgument('argumentA') ?? 'NULL'));
        $output->writeln('OptionA: '.($input->getOption('optionA') ?? 'NULL'));

        return 0;
    }

    protected function configure()
    {
        $this
            ->setDescription('Command used for feature tests.')
            ->setHelp('This command will be used during feature tests.')
        ;

        $this->addArgument('argumentA', InputArgument::OPTIONAL, '[First Argument]');
        $this->addOption('optionA', '-o', InputOption::VALUE_OPTIONAL, '[First Option]');
    }
}
