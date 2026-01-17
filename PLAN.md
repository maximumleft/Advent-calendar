# План реализации Адвент-календаря

## Описание проекта
Адвент-календарь с групповой функциональностью, где пользователи могут создавать группы, добавлять события в общий календарь, комментировать события, подтверждать/отклонять участие и делать рассылку по подписчикам группы.

## Технический стек
- **Backend**: Laravel 10+
- **Frontend**: Vue.js 3 (Composition API)
- **Admin Panel**: Filament 3.x
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Sanctum или Breeze

---

## 1. Структура базы данных

### 1.1 Модели и миграции

#### `users` (стандартная таблица Laravel)
- id
- name
- email
- email_verified_at
- password
- remember_token
- timestamps

#### `groups` (группы)
- id
- name (название группы)
- description (описание, nullable)
- owner_id (user_id создателя группы)
- color (цвет группы для визуализации, nullable)
- is_public (публичная/приватная группа)
- created_at, updated_at

#### `group_members` (участники групп)
- id
- group_id (foreign key)
- user_id (foreign key)
- role (enum: 'owner', 'admin', 'member')
- status (enum: 'pending', 'accepted', 'rejected')
- subscribed (boolean, подписан на рассылку)
- joined_at
- created_at, updated_at

#### `events` (события в календаре)
- id
- group_id (foreign key)
- user_id (создатель события, foreign key)
- title (название события)
- description (описание, nullable)
- event_date (дата события)
- event_time (время события, nullable)
- location (место проведения, nullable)
- color (цвет события, nullable)
- created_at, updated_at

#### `event_participants` (участники событий)
- id
- event_id (foreign key)
- user_id (foreign key)
- status (enum: 'confirmed', 'declined', 'pending')
- created_at, updated_at

#### `event_comments` (комментарии к событиям)
- id
- event_id (foreign key)
- user_id (foreign key)
- comment (текст комментария)
- parent_id (для вложенных комментариев, nullable)
- created_at, updated_at

#### `notifications` (уведомления/рассылки)
- id
- group_id (foreign key)
- user_id (отправитель, foreign key)
- subject (тема рассылки)
- message (текст сообщения)
- sent_at
- created_at, updated_at

#### `notification_recipients` (получатели рассылок)
- id
- notification_id (foreign key)
- user_id (foreign key)
- read_at (nullable)
- created_at, updated_at

---

## 2. Модели Laravel (Eloquent)

### 2.1 Group Model
```php
// app/Models/Group.php
- relationships:
  - owner() -> belongsTo(User)
  - members() -> hasMany(GroupMember)
  - events() -> hasMany(Event)
  - notifications() -> hasMany(Notification)
- methods:
  - isMember($userId)
  - canEdit($userId)
  - getSubscribedMembers()
```

### 2.2 GroupMember Model
```php
// app/Models/GroupMember.php
- relationships:
  - group() -> belongsTo(Group)
  - user() -> belongsTo(User)
```

### 2.3 Event Model
```php
// app/Models/Event.php
- relationships:
  - group() -> belongsTo(Group)
  - creator() -> belongsTo(User)
  - participants() -> hasMany(EventParticipant)
  - comments() -> hasMany(EventComment)
- methods:
  - getParticipantsCount()
  - isParticipant($userId)
```

### 2.4 EventParticipant Model
```php
// app/Models/EventParticipant.php
- relationships:
  - event() -> belongsTo(Event)
  - user() -> belongsTo(User)
```

### 2.5 EventComment Model
```php
// app/Models/EventComment.php
- relationships:
  - event() -> belongsTo(Event)
  - user() -> belongsTo(User)
  - parent() -> belongsTo(EventComment, 'parent_id')
  - replies() -> hasMany(EventComment, 'parent_id')
```

### 2.6 Notification Model
```php
// app/Models/Notification.php
- relationships:
  - group() -> belongsTo(Group)
  - sender() -> belongsTo(User)
  - recipients() -> hasMany(NotificationRecipient)
```

---

## 3. API Endpoints (Laravel Routes)

### 3.1 Группы (Groups)
```
GET    /api/groups                    - список групп пользователя
POST   /api/groups                    - создать группу
GET    /api/groups/{id}               - получить группу
PUT    /api/groups/{id}               - обновить группу
DELETE /api/groups/{id}               - удалить группу
POST   /api/groups/{id}/invite        - пригласить пользователя
POST   /api/groups/{id}/join          - присоединиться к группе
DELETE /api/groups/{id}/leave         - покинуть группу
GET    /api/groups/{id}/members       - список участников
PUT    /api/groups/{id}/members/{userId} - изменить роль участника
POST   /api/groups/{id}/subscribe     - подписаться на рассылку
DELETE /api/groups/{id}/unsubscribe   - отписаться от рассылки
```

### 3.2 События (Events)
```
GET    /api/groups/{groupId}/events   - список событий группы
POST   /api/groups/{groupId}/events   - создать событие
GET    /api/events/{id}               - получить событие
PUT    /api/events/{id}               - обновить событие
DELETE /api/events/{id}               - удалить событие
```

### 3.3 Участники событий (Event Participants)
```
POST   /api/events/{id}/participate   - подтвердить/отклонить участие
GET    /api/events/{id}/participants  - список участников события
DELETE /api/events/{id}/participants/{userId} - удалить участника
```

### 3.4 Комментарии (Comments)
```
GET    /api/events/{id}/comments      - список комментариев
POST   /api/events/{id}/comments      - создать комментарий
PUT    /api/comments/{id}             - обновить комментарий
DELETE /api/comments/{id}             - удалить комментарий
```

### 3.5 Рассылки (Notifications)
```
GET    /api/groups/{id}/notifications - история рассылок
POST   /api/groups/{id}/notifications - создать и отправить рассылку
GET    /api/notifications             - мои уведомления
PUT    /api/notifications/{id}/read   - отметить как прочитанное
```

---

## 4. Контроллеры Laravel

### 4.1 GroupController
- index() - список групп
- store() - создание группы
- show() - детали группы
- update() - обновление группы
- destroy() - удаление группы
- invite() - приглашение пользователя
- join() - присоединение к группе
- leave() - выход из группы
- members() - список участников
- updateMember() - изменение роли участника
- subscribe() - подписка на рассылку
- unsubscribe() - отписка от рассылки

### 4.2 EventController
- index() - список событий группы
- store() - создание события
- show() - детали события
- update() - обновление события
- destroy() - удаление события

### 4.3 EventParticipantController
- participate() - подтверждение/отклонение участия
- index() - список участников
- destroy() - удаление участника

### 4.4 EventCommentController
- index() - список комментариев
- store() - создание комментария
- update() - обновление комментария
- destroy() - удаление комментария

### 4.5 NotificationController
- index() - список рассылок группы
- store() - создание и отправка рассылки
- myNotifications() - мои уведомления
- markAsRead() - отметить как прочитанное

---

## 5. Валидация и Request классы

### 5.1 StoreGroupRequest
- name: required|string|max:255
- description: nullable|string
- color: nullable|string|regex:/^#[0-9A-Fa-f]{6}$/
- is_public: boolean

### 5.2 StoreEventRequest
- title: required|string|max:255
- description: nullable|string
- event_date: required|date
- event_time: nullable|date_format:H:i
- location: nullable|string|max:255
- color: nullable|string|regex:/^#[0-9A-Fa-f]{6}$/

### 5.3 ParticipateEventRequest
- status: required|in:confirmed,declined

### 5.4 StoreCommentRequest
- comment: required|string|max:1000
- parent_id: nullable|exists:event_comments,id

### 5.5 StoreNotificationRequest
- subject: required|string|max:255
- message: required|string

---

## 6. Политики доступа (Policies)

### 6.1 GroupPolicy
- viewAny() - просмотр списка групп
- view() - просмотр группы
- create() - создание группы
- update() - обновление (только owner/admin)
- delete() - удаление (только owner)
- invite() - приглашение (owner/admin)
- manageMembers() - управление участниками (owner/admin)

### 6.2 EventPolicy
- viewAny() - просмотр событий группы
- view() - просмотр события
- create() - создание события (член группы)
- update() - обновление (создатель или admin группы)
- delete() - удаление (создатель или admin группы)

### 6.3 EventCommentPolicy
- create() - создание комментария (член группы)
- update() - обновление (только автор)
- delete() - удаление (автор или admin группы)

### 6.4 NotificationPolicy
- create() - создание рассылки (owner/admin группы)
- view() - просмотр рассылок группы

---

## 7. Frontend (Vue.js 3)

### 7.1 Структура компонентов

```
resources/js/
├── components/
│   ├── Groups/
│   │   ├── GroupList.vue
│   │   ├── GroupCard.vue
│   │   ├── GroupForm.vue
│   │   ├── GroupDetails.vue
│   │   ├── GroupMembers.vue
│   │   └── GroupInvite.vue
│   ├── Events/
│   │   ├── CalendarView.vue
│   │   ├── EventList.vue
│   │   ├── EventCard.vue
│   │   ├── EventForm.vue
│   │   ├── EventDetails.vue
│   │   ├── EventParticipants.vue
│   │   └── EventComments.vue
│   ├── Comments/
│   │   ├── CommentList.vue
│   │   ├── CommentItem.vue
│   │   └── CommentForm.vue
│   ├── Notifications/
│   │   ├── NotificationList.vue
│   │   ├── NotificationForm.vue
│   │   └── NotificationItem.vue
│   └── Shared/
│       ├── Calendar.vue
│       ├── ColorPicker.vue
│       └── UserAvatar.vue
├── composables/
│   ├── useGroups.js
│   ├── useEvents.js
│   ├── useComments.js
│   ├── useNotifications.js
│   └── useAuth.js
├── services/
│   ├── api.js
│   └── groups.js
│   └── events.js
│   └── comments.js
│   └── notifications.js
└── views/
    ├── Dashboard.vue
    ├── Groups/
    │   ├── Index.vue
    │   └── Show.vue
    └── Events/
        └── Calendar.vue
```

### 7.2 Основные страницы

#### Dashboard.vue
- Список групп пользователя
- Ближайшие события из всех групп
- Непрочитанные уведомления

#### Groups/Index.vue
- Список всех групп
- Фильтры (мои группы, публичные группы)
- Кнопка создания новой группы

#### Groups/Show.vue
- Информация о группе
- Календарь событий
- Список участников
- Управление группой (для owner/admin)

#### Events/Calendar.vue
- Календарное представление событий
- Фильтры по датам
- Модальное окно создания события
- Клик по событию - детали

### 7.3 Композаблы (Composables)

#### useGroups.js
```javascript
- groups (ref)
- loading (ref)
- fetchGroups()
- createGroup(data)
- updateGroup(id, data)
- deleteGroup(id)
- joinGroup(id)
- leaveGroup(id)
```

#### useEvents.js
```javascript
- events (ref)
- loading (ref)
- fetchEvents(groupId)
- createEvent(groupId, data)
- updateEvent(id, data)
- deleteEvent(id)
- participateEvent(eventId, status)
```

#### useComments.js
```javascript
- comments (ref)
- loading (ref)
- fetchComments(eventId)
- createComment(eventId, data)
- updateComment(id, data)
- deleteComment(id)
```

#### useNotifications.js
```javascript
- notifications (ref)
- loading (ref)
- fetchNotifications(groupId)
- sendNotification(groupId, data)
- markAsRead(id)
```

### 7.4 API сервисы

#### api.js (базовый класс)
- axios instance с interceptors
- обработка ошибок
- токены авторизации

#### groups.js
- все методы для работы с группами

#### events.js
- все методы для работы с событиями

#### comments.js
- все методы для работы с комментариями

#### notifications.js
- все методы для работы с рассылками

---

## 8. Filament Admin Panel

### 8.1 Ресурсы Filament

#### GroupResource
- Таблица: список групп с фильтрами
- Форма создания/редактирования
- Действия: просмотр участников, статистика
- Отношения: members, events

#### EventResource
- Таблица: список событий
- Фильтры: по группе, по дате
- Форма создания/редактирования
- Отношения: participants, comments

#### UserResource
- Таблица: список пользователей
- Форма создания/редактирования
- Отношения: groups, events

#### NotificationResource
- Таблица: история рассылок
- Фильтры: по группе, по дате
- Просмотр получателей

### 8.2 Виджеты Filament

#### GroupsStatsWidget
- Количество групп
- Количество участников
- Активные группы

#### EventsStatsWidget
- Количество событий
- Предстоящие события
- События сегодня

---

## 9. Сервисы и Jobs

### 9.1 NotificationService
```php
// app/Services/NotificationService.php
- sendToGroup($groupId, $subject, $message)
  - получает список подписчиков
  - создает запись Notification
  - создает записи NotificationRecipient
  - отправляет email/уведомления (через Job)
```

### 9.2 SendNotificationJob
```php
// app/Jobs/SendNotificationJob.php
- отправка email уведомлений
- отправка push уведомлений (если нужно)
- логирование отправки
```

### 9.3 EventService
```php
// app/Services/EventService.php
- getUpcomingEvents($groupId)
- getEventsByDateRange($groupId, $startDate, $endDate)
- getEventStatistics($eventId)
```

---

## 10. Этапы разработки

### Этап 1: Настройка проекта и база данных
1. Установка Laravel
2. Настройка базы данных
3. Создание миграций для всех таблиц
4. Создание моделей с relationships
5. Настройка аутентификации (Sanctum/Breeze)

### Этап 2: Backend API
1. Создание Request классов для валидации
2. Создание Policies для авторизации
3. Создание контроллеров
4. Настройка API routes
5. Тестирование API (Postman/Insomnia)

### Этап 3: Frontend - Группы
1. Установка Vue.js 3
2. Настройка роутинга (Vue Router)
3. Создание API сервисов
4. Создание composables для групп
5. Компоненты для работы с группами
6. Страницы групп

### Этап 4: Frontend - События
1. Композаблы для событий
2. Компонент календаря
3. Компоненты событий
4. Страница календаря
5. Интеграция с группами

### Этап 5: Frontend - Комментарии и участники
1. Композаблы для комментариев
2. Компоненты комментариев
3. Компоненты участников событий
4. Интеграция с событиями

### Этап 6: Рассылки
1. NotificationService
2. SendNotificationJob
3. API endpoints для рассылок
4. Frontend компоненты рассылок
5. Email шаблоны

### Этап 7: Filament Admin
1. Установка Filament
2. Создание ресурсов
3. Настройка виджетов
4. Кастомизация админки

### Этап 8: Тестирование и оптимизация
1. Unit тесты для сервисов
2. Feature тесты для API
3. Тестирование UI
4. Оптимизация запросов (eager loading)
5. Кэширование при необходимости

### Этап 9: Дополнительные функции
1. Поиск по событиям/группам
2. Фильтры и сортировка
3. Экспорт календаря (iCal)
4. Уведомления в реальном времени (WebSockets/Pusher)
5. Мобильная адаптация

---

## 11. Дополнительные соображения

### 11.1 Безопасность
- CSRF защита
- Rate limiting для API
- Валидация всех входных данных
- Проверка прав доступа через Policies
- Sanitization пользовательского контента

### 11.2 Производительность
- Eager loading для relationships
- Пагинация для списков
- Индексы в БД для часто используемых полей
- Кэширование групп и событий
- Оптимизация запросов к БД

### 11.3 UX/UI
- Адаптивный дизайн
- Загрузочные состояния
- Обработка ошибок
- Toast уведомления
- Модальные окна для форм

### 11.4 Email уведомления
- Приглашение в группу
- Новое событие в группе
- Новый комментарий к событию
- Рассылка от группы
- Напоминание о событии (за день до)

---

## 12. Примеры ключевых файлов

### 12.1 Миграция groups
```php
Schema::create('groups', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->foreignId('owner_id')->constrained('users');
    $table->string('color', 7)->nullable();
    $table->boolean('is_public')->default(false);
    $table->timestamps();
});
```

### 12.2 Миграция events
```php
Schema::create('events', function (Blueprint $table) {
    $table->id();
    $table->foreignId('group_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained();
    $table->string('title');
    $table->text('description')->nullable();
    $table->date('event_date');
    $table->time('event_time')->nullable();
    $table->string('location')->nullable();
    $table->string('color', 7)->nullable();
    $table->timestamps();
});
```

### 12.3 Пример API метода (GroupController)
```php
public function store(StoreGroupRequest $request)
{
    $group = Group::create([
        'name' => $request->name,
        'description' => $request->description,
        'owner_id' => auth()->id(),
        'color' => $request->color,
        'is_public' => $request->is_public ?? false,
    ]);

    // Автоматически добавляем создателя как owner
    $group->members()->create([
        'user_id' => auth()->id(),
        'role' => 'owner',
        'status' => 'accepted',
        'subscribed' => true,
    ]);

    return response()->json($group->load('owner', 'members'), 201);
}
```

---

## 13. Зависимости (composer.json)

```json
{
    "require": {
        "laravel/framework": "^10.0",
        "laravel/sanctum": "^3.2",
        "filament/filament": "^3.0"
    }
}
```

## 14. Зависимости (package.json)

```json
{
    "dependencies": {
        "vue": "^3.3.0",
        "vue-router": "^4.2.0",
        "axios": "^1.5.0",
        "@fullcalendar/vue3": "^6.1.0",
        "@fullcalendar/daygrid": "^6.1.0"
    }
}
```

---

## Заключение

Этот план охватывает все основные аспекты разработки адвент-календаря. Рекомендуется следовать этапам последовательно, тестируя каждый этап перед переходом к следующему. При необходимости можно добавлять дополнительные функции по ходу разработки.
