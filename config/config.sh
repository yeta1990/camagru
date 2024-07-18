#sudo chown -R 1000:1001 /var/www/html/
DB_FOLDER="/var/www/html/db"
DB_PATH=$DB_FOLDER"/db.db"
CONFIG_FOLDER="/var/www/html/config"
UPLOADS_PATH="/var/www/html/uploads"

if [ ! -f "$DB_PATH" ]; then
    mkdir -p $DB_FOLDER
    sqlite3 $DB_PATH "VACUUM;"
    chmod -R 777 $DB_FOLDER

    echo $DB_PATH
    for sql_file in "$CONFIG_FOLDER"/*.sql; 
    do
        if [ -f "$sql_file" ]; then
            echo "Executing $sql_file..."
            sqlite3 "$DB_PATH" < "$sql_file"
        fi
    done
fi

if [ ! -f "$UPLOADS_PATH" ]; then
    mkdir -p /var/www/html/uploads
    chmod -R 777 /var/www/html/uploads/
fi

apache2-foreground
