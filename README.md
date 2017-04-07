# Common 

Common package of ZVER packages

```
composer require zver/common
```

## Methods

*  \Zver\Common
    * getDefaultEncoding() - UTF-8
    * replaceSlashesToPlatformSlashes($path)
    * convertToDefaultEncoding($string, $fromEncoding)
    * registerAutoloadClassesFrom($directory)
    * isWindowsOS()
    * isLinuxOS()
    * isProcessRunning()
    * execShellSync($command)
    * execShellAsync($command)
    * getCommonPath($path1, $path2)
    * getDirectoryContent($directory)
    * getDirectoryContentRecursive($directory)
    * getOSName()


## Traits

*  \Zver\Package\Test
    * foreachTrue(array $values)
    * foreachFalse(array $values)
    * foreachSame(array $values)
    * foreachNotSame(array $values)
    * getPackageDir($path='')
    

Created using [Package template](https://github.com/s4urp8n/package-template)
