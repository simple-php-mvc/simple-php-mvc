<?php

namespace MVC\Tests\TestModule\Command;

use Symfony\Component\Console\Command\Command;

/**
 * TestCommand
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class TestCommand extends Command
{

    protected function configure()
    {
        $this->setName('test')
            ->setDescription('Ejemplo de comando')
            ->setHelp(<<<EOF
Este es un ejemplo de comando
EOF
        );
    }

}
