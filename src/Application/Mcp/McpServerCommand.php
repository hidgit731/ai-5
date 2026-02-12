<?php

namespace App\Application\Mcp;

use PhpMcp\Server\Server;
use PhpMcp\Server\Transports\StdioServerTransport;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(name: 'dev:mcp-server')]
class McpServerCommand extends Command
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ContainerInterface $container
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->logger->info('[MCP] Starting MCP Server...');

            $projectDir = __DIR__;

            $server = Server::make()
                ->withServerInfo('Homework 5 MCP Server', '1.0.0')
                ->withContainer($this->container)
                ->withLogger($this->logger)
                ->build();

            // Автоматически обнаруживает все классы с атрибутами #[McpTool], #[McpResource], #[McpResourceTemplate], #[McpPrompt]
            $this->logger->info('[MCP] Discovering MCP handlers...');
            $server->discover(
                basePath: $projectDir,
                scanDirs: ['Handlers'],
            );

            $this->logger->info('[MCP] Server listening on stdio transport');
            $transport = new StdioServerTransport();
            $server->listen($transport);

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->logger->error('[MCP ERROR] ' . $e->getMessage());
            $this->logger->error('[MCP STACK TRACE]');
            $this->logger->error($e->getTraceAsString());

            return Command::FAILURE;
        }
    }
}
