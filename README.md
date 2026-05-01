<div align="center">
  <img src="https://github.com/user-attachments/assets/fb3c0922-bdcf-4f74-bfa6-9b0f6f63d93e" width="600" alt="NETEORIGIN">
</div>

# NET.E
### The Ultimate Low-Quality File Server (That shouldn't work, yet it is.)

NET.E is an experimental self-hosted platform that bundles a file manager, notes,
and a handful of tiny internet utilities — all written *entirely from scratch*  
in pure PHP.  

No frameworks. No Composer. No CDN. No vendor folder.  
Just raw PHP, HTML/CSS, JavaScript that hold everything together with duct tape.

---

## 📸 Screenshots

<div align="center">
  <img width="800" alt="empty_main"  src="https://github.com/user-attachments/assets/ecb55d37-6364-4d55-a229-d01a4b980acb" />
  <p><em>Main Landing</em></p>
  <img width="800" alt="file_manager" src="https://github.com/user-attachments/assets/8054bd60-0d95-4179-b1ef-d924d15adf0c" />
  <p><em>My Files — manage your uploaded and created files</em></p>
  <img width="800" alt="memory_note"  src="https://github.com/user-attachments/assets/246b682c-14c0-42e6-a476-4425c20b56db" />
  <p><em>Memory Notes — quick notes with 6 color options</em></p>
</div>

## ✨ Features

### 📁 File Manager (My Files)
- Upload files (1 file per upload, max 1GB)
- View, download, rename, and move files  
- Folder system with recursion-safe move logic  
  → prevents dropping a folder into its own descendants (anti–time paradox system)
- Internal file sharing
- Integrity check + monitor system (Tamper Shield, Tamper sentinel)
- Live text file content editable so you can write your ultimate sitcom in here. (Only save if content size is lower than 50MB, If it's larger than that then it's the DB dump, not a ordinary text file.)
- Quick access sidebar for recently viewed files — session-based, stores up to 10 unique recently accessed files (Most useless feature you'll ever need.)

### 📝 Memory Notes
- Create notes instantly  
- Auto-save when you stop typing  
- 6 colors to choose from  
- Public note mode  
- Delete note with the small trash bin icon that quietly judges your life choices

### 🔧 Small Utility Pack
Fun little built-ins:
- Multi-search gateway  
- Mini iframe browser  
- Quick hardcoded links  
- YouTube “no-cookie” viewer  
- Tiny drawing canvas for masterpieces nobody asked for
- D.U.M.B "AI" with existential crisis to answer your daily life advice

### ⚙️ User Preferences
- Change background  
- Change password

---

## 🛠️ Tech Stack (if you squint)
- Apache 2.4+
- PHP 8+
- MySQL 8+
- HTML + CSS (global + inline + local, the holy trinity)
- JavaScript (minimalistic by fear, not by design)
- MVC-ish structure:  
  - **M** = My Logic  
  - **V** = Very Homemade  
  - **C** = Continue At Your Own Risk  

---

## 📦 Installation (so simple that you might think this is a joke)

### 0. Pre Setup
Get yourself Apache + PHP + MySQL stack. You can get XAMPP or Laragon, Then put whole system in `/Deploy` folder to www, htdocs.

### 1. Database Setup
Create a database and import the schema provided in `Backup/`.
It's already have Admin user inside.

### 2. Create `config.ini` (required)
Place in:  
`Element/Database/Database_Config/config.ini`  

config.ini for example
```
[section]
servername = 127.0.0.1
dbname = your_db << It's your DB name when import the DB.
username = root
pass = null << if you don't have password
```


### 3. Set Apache/PHP Hardening (optional but recommended)
Apache
```
ServerTokens Prod
ServerSignature Off
```

PHP
```
expose_php = Off
```

### 4. Access As Admin (It's done!)
To find the admin login page you need to access to `login_admin.php` in the project root 
and verify your key file here. The default key can be find in the Extra folder. Changing default admin key is recommended.

### 5. Additional Setup
Please refer to [note](https://github.com/PHUC-GIT/Project_NETE/blob/main/note.md) in this repo if you want to learn how to manage user and extra configure.

---

## 📌 Known Limitations (a.k.a design choices)
- Admin management requires manual DB update hash value.
- Desktop-focused UI (mobile users are warriors, they’ll figure it out)
- No multi-upload (upload archives instead)
- Folder and file deletion is permanent (we don’t do regrets here)

---

## 🔒 Security Audit

<div align="center">
  <img width="350" alt="ZAP result top" src="https://github.com/user-attachments/assets/d47fb977-4c10-44d3-a884-31df777cefc0" /><img width="350" alt="ZAP result bottom" src="https://github.com/user-attachments/assets/1fc005c3-7be0-48e5-bf78-1c15e32d737c" />
  <p><em>Active scan with OWASP ZAP 2.17.0 using `ATTACK mode` and user authenticated attack base</em></p>
</div>

---

## 📜 License
AGPL-3.0 Meaning you can use, modify, and fork NET.E freely.
But if you host this on the web, you must share your source code too. No hiding your duct tape.
If something explodes, corrupts, disappears, or becomes self-aware, that’s on you (it works on my machine™).

---

## 🧩 Why This Exists
Because building things from scratch is fun.  
Because frameworks update weekly and patch CVEs monthly.  
Because I like knowing every screw, wire, and duct tape inside my system. 

---

## Credits
**Libraries:**
- [jQuery 3.6.3](https://jquery.com/) - MIT License
- [Boxicons V2](https://boxicons.com/) - MIT License
- [Feathericons](https://feathericons.com/) - MIT License
- [Roboto Fonts](https://fonts.google.com/specimen/Roboto) - OFL-1.1 License

**Wallpapers:**
- [Pawel Czerwinski](https://unsplash.com/@pawel_czerwinski) - [Link to image](https://unsplash.com/photos/a-black-and-blue-abstract-background-with-squares-and-rectangles-O_lLr6e8NtQ) - [Unsplash](https://unsplash.com/license)
- [Quang Nguyen Vinh](https://www.pexels.com/photo/2-people-on-the-boat-2166711/) - Pexels
- [Stein Egil Liland](https://www.pexels.com/photo/aurora-borealis-1933239/) - Pexels
- [Rostislav Uzunov](https://www.pexels.com/photo/purple-and-pink-diamond-on-blue-background-5011647/) - Pexels
- [Lev Strelchenko](https://www.pexels.com/photo/trees-and-fern-in-forest-17893049/) - Pexels

If this project sparks joy for you too — welcome aboard!
Please ⭐ it so I know someone touched my project! 🤣
Made with true passion and ❤!
