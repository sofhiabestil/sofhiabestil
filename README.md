# Sofhia Web Portfolio (Simple Scaffold)

This is a minimal responsive portfolio scaffold using HTML, Bootstrap, CSS, JavaScript and PHP (for contact handling).

Quick run (PHP built-in server):

```powershell
cd "c:\Users\piami\Downloads\Sofhia Web Portfolio"
php -S localhost:8000
```

Open http://localhost:8000/ in your browser.

Notes:
- The contact form posts to `contact.php`. If your PHP environment has no mail configured, messages will be appended to `messages.txt`.
- Update the recipient email in `contact.php` (`$to`) to receive real emails.
