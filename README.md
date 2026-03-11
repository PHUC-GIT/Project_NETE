<div align="center">
  <img width="600" height="585" alt="Project_NETE_Logo" src="https://github.com/user-attachments/assets/2b3a2e0d-bddb-4ebf-8571-cd0d6b04a3a6" />
</div>

# NET.E: Crystal Gems  
### The Ultimate Low-Quality File Server (That shouldn't work, yet it is.)

NET.E is an experimental self-hosted platform that bundles a file manager, notes,
and a handful of tiny internet utilities — all written *entirely from scratch*  
in pure PHP.  

No frameworks. No Composer. No CDN. No vendor folder.  
Just raw PHP, HTML/CSS, and less than 120 lines of JavaScript that hold everything together with duct tape.

---

## 📸 Screenshots

<div align="center">
  <img width="800" alt="login_screen" src="https://github.com/user-attachments/assets/924b2a2b-eb7e-4aa8-83bc-e689b5d5db01" />
  <p><em>Login</em></p>
  <img width="800" alt="empty_main" src="https://github.com/user-attachments/assets/f7268a83-e095-4299-8a71-0dc1e24e296e" />
  <p><em>Main Landing</em></p>
  <img width="800" alt="image" src="https://github.com/user-attachments/assets/a2f5a57b-3df4-47e4-84b7-a0a488250056" />
  <p><em>My Files — manage your uploaded and created files</em></p>
  <img width="800" alt="image" src="https://github.com/user-attachments/assets/adebc523-7226-4022-ac73-25ab0426bf63" />
  <p><em>Memory Notes — quick notes with 6 color options</em></p>
</div>

## ✨ Features

### 📁 File Manager (My Files)
- Upload files (1 file per upload, max 1GB)
- View, download, rename, and move files  
- Folder system with recursion-safe move logic  
  → prevents dropping a folder into its own descendants (anti–time paradox system)
- Internal file sharing
- Integrity check (Tamper Shield)  
- Delete files & folders permanently — no trash bin (we respect commitment)
- Live text file content editable so you can write your ultimate sit-con in here. (Only save if content size is lower than 50MB, If it's larger than that then it's the DB dump, not a ordinary text file.)

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

### ⚙️ User Preferences
- Change background  
- Change password

---

## 🛠️ Tech Stack (if you squint)
- Apache 2.4
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
Get yourself Apache + PHP + MySQL stack. You can get XAMPP or Laragon, Then put whole folder inside www, htdocs.

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
To find the admin login page you need to access by `https://[yoursite]/login_admin.php`
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
  <img width="350" alt="ZAP result top" src="https://github.com/user-attachments/assets/217dbb98-bd20-4c1b-8b45-9ce72c13b24e" /><img width="350" alt="ZAP result bottom" src="https://github.com/user-attachments/assets/832824a4-e426-45da-8030-a492ce961184" />

  <p><em>Active scan with OWASP ZAP 2.17.0 using `ATTACK mode` and Authenticated attack base</em></p>
</div>

---

## 📜 License
MIT — meaning you can use, modify, and fork NET.E freely.  
If something explodes, corrupts, disappears, or becomes self-aware,  
that’s on you (it works on my machine™).

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
- [Pawel Czerwinski](https://unsplash.com/@pawel_czerwinski) - Unsplash
- [Quang Nguyen Vinh](https://www.pexels.com/photo/2-people-on-the-boat-2166711/) - Pexels
- [Stein Egil Liland](https://www.pexels.com/photo/aurora-borealis-1933239/) - Pexels
- [Rostislav Uzunov](https://www.pexels.com/photo/purple-and-pink-diamond-on-blue-background-5011647/) - Pexels
- [Lev Strelchenko](https://www.pexels.com/photo/trees-and-fern-in-forest-17893049/) - Pexels

If this project sparks joy for you too — welcome aboard.
