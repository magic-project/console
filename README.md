# Magic Console

Magic Console 是 Magic Agent CLI 使用的独立控制台组件，用于快速组织命令、解析参数选项、输出帮助信息并返回标准退出码。

## 环境要求

- PHP 8.2+
- Composer

## 安装

在当前目录安装依赖并生成 Composer 自动加载文件：

```bash
composer install
```

如果在其他项目中引用这个包，可以通过 Composer 注册依赖：

```json
{
    "require": {
        "magic/console": "*"
    }
}
```

## 快速开始

继承 `Magic\Console\Command` 创建命令，把命令注册到 `Application`，最后调用 `run()`。

```php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Magic\Console\Application;
use Magic\Console\Command;
use Magic\Console\ExitCode;
use Magic\Console\Input;
use Magic\Console\Output;

final class PingCommand extends Command
{
    public function name(): string
    {
        return 'ping';
    }

    public function description(): string
    {
        return 'Check whether the console app is responding.';
    }

    public function execute(Input $input, Output $output): int
    {
        $output->success('pong');

        return ExitCode::SUCCESS;
    }
}

$app = new Application('demo', '1.0.0');
$app->add(new PingCommand());

exit($app->run());
```

运行命令：

```bash
php demo.php ping
php demo.php --help
php demo.php ping --help
```

## 使用示例

仓库内提供了一个可直接运行的示例：`examples/hello.php`。

```bash
php examples/hello.php hello Taylor
php examples/hello.php hello Taylor --times=3
php examples/hello.php hi Taylor --verbose
php examples/hello.php hello --help
```

示例命令核心代码：

```php
final class HelloCommand extends Command
{
    public function name(): string
    {
        return 'hello';
    }

    public function arguments(): array
    {
        return [
            new Argument('name', 'Name to greet.', required: true),
        ];
    }

    public function options(): array
    {
        return [
            new Option('times', description: 'Number of greetings to print.', acceptsValue: true, default: '1'),
        ];
    }

    public function execute(Input $input, Output $output): int
    {
        $name = $input->argument(0);
        $times = max(1, (int) $input->option('times', '1'));

        for ($index = 0; $index < $times; $index++) {
            $output->success(sprintf('Hello, %s!', $name));
        }

        return ExitCode::SUCCESS;
    }
}
```

## 命令定义

一个命令可以定义：

- `name()`：命令名称。
- `description()`：帮助信息中的命令描述。
- `arguments()`：位置参数。
- `options()`：命令选项。
- `aliases()`：命令别名。
- `before()` 和 `after()`：命令执行前后的钩子。
- `execute()`：命令主体逻辑和退出码。

必填参数会在 `execute()` 执行前自动校验。

## 输入

通过 `Input` 读取位置参数和选项：

```php
$name = $input->argument(0, 'World');
$verbose = $input->isVerbose();
$configPath = $input->config();
$value = $input->option('name', 'default');
```

支持的选项形式：

```bash
--flag
--name=value
--name value
```

需要接收值的选项要使用 `new Option(..., acceptsValue: true)` 声明。

## 输出

通过 `Output` 提供的辅助方法输出不同类型的信息：

```php
$output->writeln('Plain text');
$output->success('Done');
$output->warning('Be careful');
$output->failure('Something failed');
$output->verbose('Only shown with --verbose');
```

`--quiet` 会关闭普通输出，错误输出仍然会写入 stderr。

## 全局选项

每个应用都支持以下全局选项：

```text
--help              Display help
--version           Display version
--no-interaction    Disable interactive prompts
--verbose, -v       Display verbose output
--quiet, -q         Suppress normal output
--debug             Display debug information
--cwd=<path>        Set working directory
--config=<path>     Set config file
```

## 退出码

使用 `Magic\Console\ExitCode` 中的常量返回退出码：

```php
return ExitCode::SUCCESS;
return ExitCode::FAILURE;
return ExitCode::INVALID_ARGUMENT;
```

未知命令、非法选项、缺少必填参数和命令执行失败会由内置异常处理器转换为非零退出码。
