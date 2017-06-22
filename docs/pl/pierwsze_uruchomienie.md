Przygotowanie yourCloud do pierwszego uruchomienie
==================================================

Aby poprawnie przygotować yourCloud do działania wykonaj następujące kroki:

1. Skopiuj wszystkie pliki do głównego katalogu Apache lub ngnix. 
2. Utwórz nowego użytkownika w bazie danych MySQL oraz nową bazę danych z pełnymi prawami. Aby uniknąć problemów z kodowaniem znaków należy użyć kodowania UTF-8
3. Stwórz katalog, w którym będą przechowywane pliku użytkowników, a następnie nadaj do niego uprawnienia do odczytu i zapisu (rw- lub rwx) dla użytkownika serwera www. W przypadku apache2 "www-data". Aby to zrobić, wykonaj następujące polecenia:
`
chmod -R 755 <ścieżka do folderu>
chown www-data:www-data <ścieżka do folderu>
`
4. Wejdź na adres swojego serwera np. http://127.0.0.1 jeśli strona pierwszej konfiguracji nie uruchamia się przejdź na stronę http://<adres twojego serwera>/first_setup/step_1
5. Przejdź całą konfigurację
6. Po prawidłowym przejściu całej konfiguracji yourCloud będzie gotowy do użytku :)