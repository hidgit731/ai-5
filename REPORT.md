# Отчёт по заданию #5

---

### Идея

Я в голове прокручивал такой сценарий при создании этой работы. Вы разработчик. Клонируете репозиторий своего рабочего проекта. 
С помощью docker compose поднимаете локально и там уже есть mcp-сервер. Я хотел внедрить его не отдельным файлом, а
именно сделать возможность запускать его внутри фреймворка, на котором сделано основное приложение, чтобы иметь возможность 
пользоваться уже готовыми подключениями к БД, к контейнеру зависимостей, которые даёт фреймворк и пр. MCP-сервер должен быть 
только для разработки.

### Разберитесь в принципах MCP
Клиент соединяется с MCP-сервером через JSON-RPC handshake (initialize, result, initialized, создаётся долгоживущая сессия).
Tool в моём сервере это функция, которую может вызвать LLM с помощью клиента на основе описания этого tool. Она может 
выполнить любой возможный код.

### Чему научился:
1. Запускать MCP-сервер на языке PHP
2. Подключать клиент к серверу (Cursor и Claude Code CLI)
3. Попрактиковался в написании и использовании tools

### Как запускал сервер:
1. Использовал пакет https://github.com/php-mcp/server (установлен как dev зависимость)
2. Я решил сделать 2 tool, т.к. в требованиях "минимум 2–4 инструмента". Я понял, как делать tools, поэтому не стал делать больше двух. Плюс время поджимает.
3. Инструкция по запуску и подключению в `/README.md`
4. MCP-сервер реализован в файле `src/Application/Mcp/McpServerCommand.php` как "консольная команда" в контексте фреймворка. 
Общается с клиентом по STDIO.
5. Tools описаны в директории `src/Application/Mcp/Handlers/`, которую "сканирует" сервер при инициализации.
    1. Tool `create_mock_courses_with_relations` для создания тестовых записей в базе. Возвращает массив созданных записей.
        + Реализован в файле `src/Application/Mcp/Handlers/CourseHandler.php` L23–L40
        + Логируется в файл `var/log/mcp.log`. Уточню, что логи, которые будут ниже, описал не я, а они от пакета из п.1. 
        Я только настроил их вывод в файл. В файл, потому что в stdout/stderr нельзя выводить - агент "слушает" их.
        + Примеры запросов пользователя:
            + "Добавь 3 тестовых курса в базу"
            + "Запиши в базу 3 курса"
            + "Добавь 3 mock курса в базу"
        + Пример логов, которые пишет при этом сервер:
        ```
        [2026-02-11T21:53:33.980007+00:00] mcp.DEBUG: Message received. {"sessionId":"stdio","message":{"PhpMcp\\Schema\\JsonRpc\\Request":{"jsonrpc":"2.0","id":4,"method":"tools/call","params":{"name":"create_mock_courses_with_relations","arguments":{"count":3},"_meta":{"claudecode/toolUseId":"toolu_01WUrUfuce7dTjdanKU3sz4Q"}}}}} []
        [2026-02-11T21:53:34.366378+00:00] mcp.DEBUG: Response sent. {"sessionId":"stdio","payload":{"PhpMcp\\Schema\\JsonRpc\\Response":{"jsonrpc":"2.0","id":4,"result":{"content":[{"type":"text","text":"[\n    {\n        \"id\": 35,\n        \"title\": \"070c6f8845a9284d716fcaff2a3c314fbfd50cc1\",\n        \"description\": null,\n        \"isActive\": true,\n        \"createdAt\": \"2026-02-11 21:53:34\",\n        \"updatedAt\": \"2026-02-11 21:53:34\"\n    },\n    {\n        \"id\": 36,\n        \"title\": \"9033408bc5324675cc719efb2b290276cbd28a32\",\n        \"description\": null,\n        \"isActive\": true,\n        \"createdAt\": \"2026-02-11 21:53:34\",\n        \"updatedAt\": \"2026-02-11 21:53:34\"\n    },\n    {\n        \"id\": 37,\n        \"title\": \"df427365e1edf619b460951b4f776b43b25d410e\",\n        \"description\": null,\n        \"isActive\": true,\n        \"createdAt\": \"2026-02-11 21:53:34\",\n        \"updatedAt\": \"2026-02-11 21:53:34\"\n    }\n]"}],"isError":false}}}} []
        ```
        + Пример ответа от модели после выполнения tool:
        ```
        Готово! В базу данных успешно добавлены 3 тестовых курса:

        1. Курс ID 35 - 070c6f8845a9284d716fcaff2a3c314fbfd50cc1
        2. Курс ID 36 - 9033408bc5324675cc719efb2b290276cbd28a32
        3. Курс ID 37 - df427365e1edf619b460951b4f776b43b25d410e

        Все курсы созданы со статусом isActive: true и связанными сущностями (урок, 2 задания, 2 навыка для каждого курса).
        ```
       
    2. Tool `cleanup_mock_courses` для удаления записей курсов из базы. Можно запустить с dryRun, чтобы было распечатано,
    что будет удалено, но самого удаления не произошло.
    
        + Реализован в файле `src/Application/Mcp/Handlers/CourseHandler.php` L42–L116
        + Логируется также в файл `var/log/mcp.log`
        + Примеры запросов пользователя:
            + "Удали 2 курса из базы dryRun"
            + "Удали 2 курса из базы"
        + Пример логов, которые пишет при этом сервер:
        ```
        [2026-02-12T09:36:25.654326+00:00] mcp.DEBUG: Message received. {"sessionId":"stdio","message":{"PhpMcp\\Schema\\JsonRpc\\Request":{"jsonrpc":"2.0","id":4,"method":"tools/call","params":{"name":"cleanup_mock_courses","arguments":{"limit":2,"dryRun":true},"_meta":{"claudecode/toolUseId":"toolu_01BZMPGkSqpusty3nfGRdFF1"}}}}} []
        [2026-02-12T09:36:25.831395+00:00] mcp.DEBUG: Response sent. {"sessionId":"stdio","payload":{"PhpMcp\\Schema\\JsonRpc\\Response":{"jsonrpc":"2.0","id":4,"result":{"content":[{"type":"text","text":"{\n    \"status\": \"dry_run\",\n    \"message\": \"Предварительный просмотр: следующие курсы будут удалены\",\n    \"deleted_count\": 2,\n    \"courses\": [\n        {\n            \"id\": 38,\n            \"title\": \"368e53b0f1a7bdf9b136827430ab26938931f937\",\n            \"description\": null,\n            \"isActive\": true,\n            \"createdAt\": \"2026-02-12 04:29:54\",\n            \"updatedAt\": \"2026-02-12 04:29:54\",\n            \"lessons_count\": 1,\n            \"modules_count\": 0\n        },\n        {\n            \"id\": 39,\n            \"title\": \"dee0e1c052dd8a3c005bb8f3132790c2354ee4f5\",\n            \"description\": null,\n            \"isActive\": true,\n            \"createdAt\": \"2026-02-12 04:29:54\",\n            \"updatedAt\": \"2026-02-12 04:29:54\",\n            \"lessons_count\": 1,\n            \"modules_count\": 0\n        }\n    ]\n}"}],"isError":false}}}} []
        [2026-02-12T09:36:48.909716+00:00] mcp.DEBUG: Message received. {"sessionId":"stdio","message":{"PhpMcp\\Schema\\JsonRpc\\Request":{"jsonrpc":"2.0","id":5,"method":"tools/call","params":{"name":"cleanup_mock_courses","arguments":{"limit":2,"dryRun":false},"_meta":{"claudecode/toolUseId":"toolu_01B5avtqHQMSkjTUoqtvus3T"}}}}} []
        [2026-02-12T09:36:48.958098+00:00] mcp.DEBUG: Response sent. {"sessionId":"stdio","payload":{"PhpMcp\\Schema\\JsonRpc\\Response":{"jsonrpc":"2.0","id":5,"result":{"content":[{"type":"text","text":"{\n    \"status\": \"success\",\n    \"message\": \"Курсы успешно удалены\",\n    \"deleted_count\": 2,\n    \"courses\": [\n        {\n            \"id\": 38,\n            \"title\": \"368e53b0f1a7bdf9b136827430ab26938931f937\",\n            \"description\": null,\n            \"isActive\": true,\n            \"createdAt\": \"2026-02-12 04:29:54\",\n            \"updatedAt\": \"2026-02-12 04:29:54\",\n            \"lessons_count\": 1,\n            \"modules_count\": 0\n        },\n        {\n            \"id\": 39,\n            \"title\": \"dee0e1c052dd8a3c005bb8f3132790c2354ee4f5\",\n            \"description\": null,\n            \"isActive\": true,\n            \"createdAt\": \"2026-02-12 04:29:54\",\n            \"updatedAt\": \"2026-02-12 04:29:54\",\n            \"lessons_count\": 1,\n            \"modules_count\": 0\n        }\n    ]\n}"}],"isError":false}}}} []
        ```
        + Пример ответов модели при запусках с `dryRun` и без него:
        ```
        Выполнен предварительный просмотр удаления. Будут удалены 2 курса:                                                                                                                                                                                                
                                                                                                                                                                                                                                                                    
        1. ID 38 - "368e53b0f1a7bdf9b136827430ab26938931f937"                                                                                                                                                                                                             
           - Активный, 1 урок                                                                                                                                                                                                                                              
           - Создан: 2026-02-12 04:29:54
        2. ID 39 - "dee0e1c052dd8a3c005bb8f3132790c2354ee4f5"                                                                                                                                                                                                             
           - Активный, 1 урок                                                                                                                                                                                                                                              
           - Создан: 2026-02-12 04:29:54

        Это режим предварительного просмотра (dryRun). Чтобы фактически удалить курсы, нужно выполнить команду без флага dryRun.
        ```
        ```
        ✓ Успешно удалены 2 курса из базы данных:                                                                                                                                                                                                                         
                                                                                                                                                                                                                                                                    
        1. ID 38 - "368e53b0f1a7bdf9b136827430ab26938931f937"
        2. ID 39 - "dee0e1c052dd8a3c005bb8f3132790c2354ee4f5"

        Курсы вместе со всеми связанными данными удалены.
        ```
