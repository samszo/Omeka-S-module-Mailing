# Omeka S Mailing Module - Architecture Documentation

## Overview

The Mailing module provides integration between Omeka S and Listmonk, enabling newsletter and mailing list management directly from the Omeka S admin interface.

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         Omeka S Core                             │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Mailing Module                              │
│                                                                   │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  Module.php                                                 │ │
│  │  - install()   : Initialize settings                        │ │
│  │  - uninstall() : Clean up settings                          │ │
│  │  - getConfigForm() : Render configuration form              │ │
│  │  - handleConfigForm() : Process configuration               │ │
│  └────────────────────────────────────────────────────────────┘ │
│                                                                   │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  Controllers (src/Controller/Admin/)                        │ │
│  │  ┌──────────────────────────────────────────────────────┐  │ │
│  │  │  IndexController                                      │  │ │
│  │  │  - indexAction()        : Dashboard                   │  │ │
│  │  │  - subscribersAction()  : Subscriber list             │  │ │
│  │  │  - listsAction()        : Mailing lists               │  │ │
│  │  │  - campaignsAction()    : Campaign management         │  │ │
│  │  └──────────────────────────────────────────────────────┘  │ │
│  └────────────────────────────────────────────────────────────┘ │
│                                                                   │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  Services (src/Service/)                                    │ │
│  │  ┌──────────────────────────────────────────────────────┐  │ │
│  │  │  ListmonkService                                      │  │ │
│  │  │  - getLists()              : Fetch all lists          │  │ │
│  │  │  - getSubscribers()        : Fetch subscribers        │  │ │
│  │  │  - getCampaigns()          : Fetch campaigns          │  │ │
│  │  │  - createSubscriber()      : Add new subscriber       │  │ │
│  │  │  - updateSubscriber()      : Update subscriber        │  │ │
│  │  │  - deleteSubscriber()      : Remove subscriber        │  │ │
│  │  │  - createCampaign()        : Create campaign          │  │ │
│  │  │  - testConnection()        : Test API connection      │  │ │
│  │  └──────────────────────────────────────────────────────┘  │ │
│  │  ┌──────────────────────────────────────────────────────┐  │ │
│  │  │  ListmonkServiceFactory                               │  │ │
│  │  │  - __invoke() : Create service with settings          │  │ │
│  │  └──────────────────────────────────────────────────────┘  │ │
│  └────────────────────────────────────────────────────────────┘ │
│                                                                   │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  Views (view/mailing/admin/index/)                          │ │
│  │  - index.phtml        : Dashboard with connection status    │ │
│  │  - subscribers.phtml  : Subscriber listing table            │ │
│  │  - lists.phtml        : Mailing lists table                 │ │
│  │  - campaigns.phtml    : Campaign listing with statistics    │ │
│  └────────────────────────────────────────────────────────────┘ │
│                                                                   │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  Assets                                                      │ │
│  │  - asset/css/mailing.css  : Module styles                   │ │
│  │  - asset/js/mailing.js    : Module JavaScript               │ │
│  └────────────────────────────────────────────────────────────┘ │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            │ HTTP REST API
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Listmonk Instance                           │
│                                                                   │
│  API Endpoints:                                                  │
│  - GET  /api/lists           : Retrieve mailing lists           │
│  - GET  /api/subscribers     : Retrieve subscribers             │
│  - POST /api/subscribers     : Create subscriber                │
│  - PUT  /api/subscribers/:id : Update subscriber                │
│  - DEL  /api/subscribers/:id : Delete subscriber                │
│  - GET  /api/campaigns       : Retrieve campaigns               │
│  - POST /api/campaigns       : Create campaign                  │
│  - PUT  /api/campaigns/:id   : Update campaign                  │
└─────────────────────────────────────────────────────────────────┘
```

## Data Flow

### 1. Module Configuration Flow

```
Admin User → Module Configure Form → handleConfigForm() → Omeka Settings
                                                           ↓
                                                    Settings Storage:
                                                    - mailing_listmonk_url
                                                    - mailing_listmonk_username
                                                    - mailing_listmonk_password
                                                    - mailing_default_list_id
```

### 2. Subscriber Viewing Flow

```
Admin User → Subscribers Page → IndexController::subscribersAction()
                                          ↓
                                 ListmonkService::getSubscribers()
                                          ↓
                                 HTTP GET to Listmonk API
                                          ↓
                                 Parse JSON Response
                                          ↓
                                 Render subscribers.phtml
                                          ↓
                                 Display Table to User
```

### 3. Campaign Monitoring Flow

```
Admin User → Campaigns Page → IndexController::campaignsAction()
                                        ↓
                               ListmonkService::getCampaigns()
                                        ↓
                               HTTP GET to Listmonk API
                                        ↓
                               Parse JSON Response with Stats
                                        ↓
                               Render campaigns.phtml
                                        ↓
                               Display Campaign Stats to User
```

## Component Details

### Module.php
- **Purpose**: Main module entry point
- **Responsibilities**:
  - Handle module lifecycle (install, uninstall)
  - Provide configuration form
  - Store/retrieve module settings
- **Key Methods**:
  - `install()`: Creates default settings
  - `uninstall()`: Removes all module settings
  - `getConfigForm()`: Generates configuration form HTML
  - `handleConfigForm()`: Processes configuration submissions

### ListmonkService
- **Purpose**: Abstraction layer for Listmonk API
- **Responsibilities**:
  - Manage HTTP communication with Listmonk
  - Handle authentication
  - Process API responses
  - Provide error handling
- **Authentication**: HTTP Basic Auth using username/password
- **Response Format**: JSON
- **Error Handling**: Returns structured arrays with success/error information

### IndexController
- **Purpose**: Handle admin interface requests
- **Responsibilities**:
  - Process user requests
  - Call appropriate service methods
  - Prepare data for views
  - Handle pagination
- **Actions**:
  - `index`: Main dashboard
  - `subscribers`: List subscribers
  - `lists`: Display mailing lists
  - `campaigns`: Show campaign statistics

### Views
- **Purpose**: Present data to users
- **Technology**: PHP templates (.phtml)
- **Features**:
  - Responsive tables
  - Status indicators
  - Pagination support
  - Breadcrumb navigation
  - Omeka S styling integration

## Configuration

### Required Settings
1. **Listmonk URL**: Base URL of Listmonk installation
2. **Username**: API authentication username
3. **Password**: API authentication password
4. **Default List ID**: (Optional) Default mailing list

### Storage
Settings are stored in Omeka S's settings table using the `Omeka\Settings` service.

## Security Considerations

1. **Authentication**: 
   - Credentials stored in Omeka S settings (database)
   - Transmitted via HTTP Basic Auth to Listmonk
   - HTTPS recommended for Listmonk connection

2. **Access Control**:
   - Module accessible only to Omeka S admin users
   - Utilizes Omeka S's built-in permission system

3. **Data Validation**:
   - Input sanitization on all form submissions
   - Output escaping in all views

## Extension Points

The module can be extended with:

1. **Additional Controllers**: Add new actions for creating/editing subscribers
2. **Event Listeners**: Hook into Omeka S events (e.g., new item created)
3. **API Methods**: Extend ListmonkService with more Listmonk API endpoints
4. **Custom Forms**: Add forms for campaign creation within Omeka S
5. **Webhooks**: Implement webhook receivers for Listmonk events

## Future Enhancements

Potential features for future versions:

- **Subscriber Management**: Create/edit/delete subscribers from Omeka S
- **Campaign Creation**: Full campaign creation interface
- **Template Management**: Manage email templates
- **Automation**: Auto-subscribe users based on Omeka S events
- **Analytics Dashboard**: Rich statistics and charts
- **Bulk Operations**: Import/export subscribers
- **Segmentation**: Create dynamic segments based on Omeka data
- **Integration**: Connect Omeka items to newsletter content

## Dependencies

- **Omeka S**: 3.0 or higher
- **PHP**: 7.4 or higher
- **Laminas Framework**: Components included with Omeka S
- **Listmonk**: Any version with REST API support
