import sqlite3
from pathlib import Path

db = sqlite3.connect("database/database.sqlite")
db.row_factory = sqlite3.Row

admin = db.execute(
    "SELECT * FROM users WHERE email = ?",
    ("restramozaik@gmail.com",)
).fetchone()

if not admin:
    raise SystemExit("Admin tidak ditemukan.")

columns = [
    "id", "name", "email", "email_verified_at",
    "password", "remember_token", "created_at", "updated_at"
]

def escape(value):
    if value is None:
        return "NULL"
    if isinstance(value, bytes):
        return "0x" + value.hex()
    return "'" + str(value).replace("\\", "\\\\").replace("'", "''") + "'"

values = ", ".join(escape(admin[column]) for column in columns)

sql = f"""INSERT INTO `users`
({", ".join(f"`{column}`" for column in columns)})
VALUES ({values});
"""

output = Path("database/exports/admin-mysql.sql")
output.write_text(sql, encoding="utf-8")

print("Export berhasil.")
print("Email:", admin["email"])
print("Password hash dipertahankan dan tidak ditampilkan.")
print("File:", output)

db.close()
