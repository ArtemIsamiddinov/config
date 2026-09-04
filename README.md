# Библиотека конфигов Demai (Config library)


Библиотека для быстрого начала работы с конфигами. В проекте используется строгая типизация, соответствует стандартам:
- PSR-4 - Автозагрузчик
- PSR-12 - Стиль кода
- PSR-11 - Интерфейс контейнеров


## Особенности


* **Поддержка различных источников данных**: Позволяет читать данные из файлов, БД и т.п.
* **Быстрое начало работы**: Позволяет быстро начать работу с конфигами, так как базовые классы уже определены.
* **Сквозной доступ к данным**: Позволяет осуществлять доступ к данным конфигов не только через цепочку вызовов, но и по сквозному имени.
* **Вложенное хранение**: Позволяет одному конфигу содержать внутри себя другой конфиг.


## Быстрый старт


### 1. Создание читателей источников конфигов
Создайте читателя источников данных, отнаследовавшись от ReaderInterface или более высокоуровневых реализаций:

```php
use Demai\Config\Reader\EnvReader;
use Demai\Config\Reader\JsonReader;
use Demai\Config\Reader\DBReader;

//Читаем из env файла
class MyEnvReader extends EnvReader
{
    public function getSource(): string
    {
        return "путь к файлу";
    }
}

//Читаем из json файла
class MyJsonReader extends JsonReader
{
    public function getSource(): string
    {
        return "путь к файлу";
    }
}

//Читаем из БД
class MyDbReader extends DBReader
{
    public function getSource(): string
    {
        return "Имя таблицы";
    }

    public function isSourceExists(): bool
    {
        return "Проверяем, сущесвует ли таблица";
    }

    public function read(): array
    {
        //Читаем данные и возвращаем их в camelCase;
        return [];
    }
}
```


### 2. Реализация конфига
Создайте класс конфига или конфигов, используемых в проекте:

```php
use Demai\Config\BaseConfig;

class MyConfig extends BaseConfig
{
    protected string $strParameter;
    protected int $intParameter = 0;

    public function __construct() {
        parent::__construct();
        $this->setReader(new MyEnvReader());
    }
}
```


### 3. Получите конфиг
Получите конфиг при помощи класса-фасада:

```php
use Demai\Config\Facade\Config;

$config = Config::get(MyConfig::class);
```


## Создание вложенных конфигов

Вы можете собирать конфиг внутри другого конфига. Обязательным условием являеться указание аттрибута ``Demai\Config\Attribute\DefaultValue``, в котором инициализируется конфиг по умолчанию.

```php
use Demai\Config\BaseConfig;
use Demai\Config\Attribute\DefaultValue;

class AppConfig extends BaseConfig
{
    //Введем свойство как пример
    protected string $key;

    // Определение свойств и установка Reader класса
}

class DBConfig extends BaseConfig
{
    //Введем свойство как пример
    protected string $name;

    // Определение свойств и установка Reader класса
}

class Config extends BaseConfig
{
    #[DefaultValue(new AppConfig)]
    protected AppConfig $app;

    #[DefaultValue(new DBConfig)]
    protected DBConfig $db;

    protected bool $someProp = false;
}
```


## Получение данных из конфига

Как было описано выше вы можете получать данные по цепочке вызовов или делать свкозное получение данных.

```php
use Demai\Config\Facade\Config as ConfigFacade;

$config = ConfigFacade::get(Config::class);

//Получаем по цепочке вызовов
$config->get('someProp');
$config->get('app')->get('key');
$config->get('db')->get('name');

//Сквозное получение данных
$config->get('app.key');
$config->get('db.name');
```


## Автоматическое заполнение данных

Библиотека выполняет ленивую инициализацию свойств.
Правила заполнения:
- Если свойство поддерживает тип ``null``, то будет установлено ``null``
- Если вы указали аттрибут ``Demai\Config\Attribute\DefaultValue``, значение будет установлено исходя из него.
- Если свойство не инициализировано и не может заполнить себя, будет выброшено исключение ``Demai\Config\Exception\InitializePropertyException``


## Типизация данных

Библиотека выполняет приведение к необходимым типам при помощи сервиса ``Demai\Config\Service\ConvertTypeService``.

Сервис выполняет проверку и приведение типов при помощи встроенных php функций ``filter_var`` или ``filter_var_array``.

При конвертации в ``bool`` сервис осуществляет дополнительную проверку значения на ``Y``,``y``,``N``,``n``.

В случае если конвертировать данные не удалось будет выброшено исключение ``Demai\Config\Exception\ArgumentTypeException``.


## Лицензия

Этот проект распространяется под лицензией GNU General Public License v3 (GPL-3.0-only). Подробности см. в файле [LICENSE](LICENSE).