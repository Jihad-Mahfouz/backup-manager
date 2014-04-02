<?php namespace BigName\DatabaseBackup\Factories;

use BigName\DatabaseBackup\Commands\Database\Mysql\DumpDatabase;
use BigName\DatabaseBackup\Commands\Storage\SaveFile;
use BigName\DatabaseBackup\Config;
use BigName\DatabaseBackup\Connections\MysqlConnection;
use BigName\DatabaseBackup\ShellProcessor;
use Symfony\Component\Process\Process;

class CommandFactory
{
    /**
     * @var \BigName\DatabaseBackup\Factories\FilesystemFactory
     */
    private $filesystemFactory;
    /**
     * @var ArchiverFactory
     */
    private $archiverFactory;
    /**
     * @var \BigName\DatabaseBackup\Config
     */
    private $config;

    public function __construct(FilesystemFactory $filesystemFactory, ArchiverFactory $archiverFactory, Config $config)
    {
        $this->filesystemFactory = $filesystemFactory;
        $this->archiverFactory = $archiverFactory;
        $this->config = $config;
    }

    public function makeSaveFileCommand($connectionName, $sourcePath, $destinationPath)
    {
        /** @noinspection PhpParamsInspection */
        return new SaveFile($this->filesystemFactory->makeFilesystemFor($connectionName), $sourcePath, $destinationPath);
    }

    public function makeDumpDatabaseCommand($databaseName, $destinationPath)
    {
        $mysql = new MysqlConnection(
            $this->config->get($databaseName, 'host'),
            $this->config->get($databaseName, 'port'),
            $this->config->get($databaseName, 'user'),
            $this->config->get($databaseName, 'pass'),
            $this->config->get($databaseName, 'database')
        );
        return new DumpDatabase($mysql, new ShellProcessor(new Process('')), $destinationPath);
    }
} 