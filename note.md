# NET.E — Deployment & Management Notes
> **PHP 8.0+ | MySQL 8.0+**

---

## 🗑️ When Deploying To Server
Delete the following as they are not needed in production:
- `3RDPARTY_LICENSES/` folder
- `Backup/` folder
- `Extra/` folder
- `.gitignore`
- `.gitattributes`
- This note file

---

## ⚙️ Database Configuration
Create `config.ini` inside `Element/Database/Database_Config/`

> ⚠️ **DO NOT commit `config.ini` to Git!**

```ini
[section]
servername = 127.0.0.1
dbname = db_name
username = root
pass = null
```

**Instructions:**
| Key | Description |
|---|---|
| `servername` | Your URL, LAN IP, or local machine IP |
| `dbname` | Your database name. Use `nete_test` for testing, `nete` for production |
| `username` | Your database admin username |
| `pass` | Your database password. Use `null` if no password is set |

---

## 🔑 Admin Access
- Login via the Admin key file located in the `Extra/` folder
- Admin accounts require **manual database management**
- Use the **Hash_Gen** program inside `Extra/` to generate your key hash
- Update the hash value directly in the Admin table in the database

---

## 👤 User Management
Users can be managed via the **Admin GUI** inside the app.
New users added by admin will have the default password `ilovenete`. 
Click the edit button on the user to change it.
Users can also change their password in Preferences.

> ⚠️ **User deletion requires manual action:**
1. Delete all associated file references in the database
2. Delete all associated notes in the database
3. Delete all user preference database entries
4. Delete User in User table
5. Finally delete the user's physical files folder located inside `User_Data\` folder
   *(Remember the user hashed folder name to avoid mistakes)*

**Storage allocation values for `storage_allocated` column:**
| Value | Size | Usage |
|---|---|---|
| `1073741824` | Exactly 1GB | Typical user |
| `104857600` | Exactly 100MB | Guest / Demo |

> ⚠️ **Never let actual `user_id = 0` in the database**

> 📝 When manually creating a user, remember to also create their row in the **preference table**. This is handled automatically when using the Admin user manager.

---

## 🔒 Apache & PHP Hardening

### Hide Apache and PHP Version
**`httpd.conf`**
```apache
ServerTokens Prod
ServerSignature Off
```

**`php.ini`**
```ini
expose_php = Off
```

### Disable Directory Indexing
In `httpd.conf` find the `Options` line and remove `Indexes`:
```apache
# Change this:
Options Indexes FollowSymLinks

# To this:
Options FollowSymLinks
```

---

## ⚡ OPcache Configuration (PHP.ini) v0.1
```ini
[opcache]
zend_extension=php_opcache.dll
opcache.enable=1
opcache.enable_cli=0                ; Change to 1 only if running CLI scripts (e.g. Composer)
opcache.memory_consumption=256      ; Recommended size in MB
opcache.interned_strings_buffer=16  ; Recommended size in MB
opcache.max_accelerated_files=10000
opcache.fast_shutdown=1             ; Only available for PHP 7.2

; --- High-Performance Configuration ---
opcache.validate_timestamps=0       ; Restart PHP when something changes
opcache.revalidate_freq=0
```

> 📝 Disable JIT if you encounter `"error fail to secure"` errors:
```ini
opcache.jit=disable
opcache.jit_buffer_size=0          ; Use this if on older PHP 8
```

---

## 🛡️ phpMyAdmin Hardening (XAMPP Only)
In `httpd-xampp.conf`:
```apache
Alias /phpmyadmin "E:/xampp/phpMyAdmin/"
<Directory "E:/xampp/phpMyAdmin">
    AllowOverride AuthConfig
    Require all denied
    Require ip 127.0.0.1
    ErrorDocument 403 /error/XAMPP_FORBIDDEN.html.var
</Directory>
```
> 📝 This restricts phpMyAdmin access to **localhost only**
