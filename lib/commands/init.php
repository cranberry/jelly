<?php

use Cranberry\CLI\Command;
use Cranberry\CLI\Format;
use Cranberry\CLI\Input;
use Cranberry\CLI\Shell;
use Cranberry\Core\File;
use Cranberry\Core\JSON;

/**
 * @name			init
 * @description		Reconfigure the template app
 * @usage			init
 */
$command = new Command\Command( 'init', 'Reconfigure the template app', function()
{
	$defaultString = new Format\String();
	$defaultString->foregroundColor( 'yellow' );

	$binDirectory = $this->app->applicationDirectory->childDir( 'bin' );
	$libDirectory = $this->app->applicationDirectory->childDir( 'lib' );

	$configFile = $libDirectory->child( 'config.json' );
	$configData = JSON::decode( $configFile->getContents(), true );

	$affirmativeResponses = ['', 'y', 'yes'];

	/*
	 * Prompt for values
	 */
	/* Application name */
	$appName = Input::prompt( "Application name:", true );

	/* Application version */
	$appVersionDefault = '0.1.0';
	$defaultString->setString( $appVersionDefault );

	$appVersion = Input::prompt( "Application version: [{$defaultString}]", false );
	if( empty( $appVersion ) )
	{
		$appVersion = $appVersionDefault;
	}

	/* Namespace */
	$namespaceDefault = ucfirst( strtolower( $appName ) );
	$defaultString->setString( $namespaceDefault );

	$namespace = Input::prompt( "PHP Namespace [{$defaultString}]:", false );
	if( empty( $namespace ) )
	{
		$namespace = $namespaceDefault;
	}

	/* New branch */
	$branchNameDefault = 'master';
	$defaultString->setString( $branchNameDefault );

	$branchName = Input::prompt( "New branch name [{$defaultString}]:", false );
	if( empty( $branchName ) )
	{
		$branchName = $branchNameDefault;
	}

	/* URL */
	$url = Input::prompt( "Project URL:", false );

	/*
	 * Git
	 */
	chdir( $this->app->applicationDirectory );

	$commandGitCurrentBranch = "git rev-parse --abbrev-ref HEAD";
	$resultCurrentBranch = Shell::exec( $commandGitCurrentBranch );
	$currentBranch = $resultCurrentBranch['output']['raw'];

	/* Delete remote */
	$commandGitRemoveOrigin = 'git remote rm origin';
	Shell::exec( $commandGitRemoveOrigin );

	/* Check out new branch */
	$commandGitCheckoutBranch = "git checkout --orphan {$branchName}";
	Shell::exec( $commandGitCheckoutBranch );

	/* Offer to delete current branch */
	$defaultString->setString( 'yes' );
	$deleteBranch = Input::prompt( "Delete branch '{$currentBranch}'? [{$defaultString}]" );

	if( in_array( strtolower( $deleteBranch ), $affirmativeResponses ) )
	{
		$commandGitDeleteBranch = "git branch -D {$currentBranch}";
		Shell::exec( $commandGitDeleteBranch );
	}

	/*
	 * Rename files
	 */
	/* Executable */
	$fileExecutable = $binDirectory->child( $this->app->name );
	$fileExecutable->rename( $appName );

	/* Source */
	$dirSrc = $libDirectory
		->childDir( 'src' )
		->childDir( $configData['namespace'] );

	$dirSrc->rename( $namespace );

	/*
	 * Update config
	 */
	$configData['version'] = $url;
	$configData['url'] = $url;
	$configData['namespace'] = $namespace;
	$configJSON = JSON::encode( $configData, JSON_PRETTY_PRINT );
	$configFile->putContents( $configJSON );

	/*
	 * Offer to delete this command
	 */
	$defaultString->setString( 'yes' );
	$deleteBranch = Input::prompt( "Delete command 'init'? [{$defaultString}]" );
	if( in_array( strtolower( $deleteBranch ), $affirmativeResponses ) )
	{
		$commandFile = new File\File( __FILE__ );
		$commandFile->delete();
	}

	/* Stage all files */
	$commandGitStageAll = 'git add .';
	Shell::exec( $commandGitDeleteBranch );
});

return $command;
