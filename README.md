# Comenzile Git folosite

### 1. git init
```bash
git init
```
Am folosit această comandă pentru a inițializa Git în folderul proiectului.

### 2. git status
```bash
git status
```
Am verificat ce fișiere au fost modificate și care trebuie adăugate.

### 3. git add
```bash
git add .
git add -A
```
Am adăugat fișierele proiectului pentru a putea face commit.

### 4. git commit
```bash
git commit -m "Initial commit - Generator Albume Foto"
```
Am salvat prima versiune a proiectului.

### 5. git branch
```bash
git branch -M main
```
Am setat ramura principală cu numele `main`.

### 6. git remote
```bash
git remote add origin https://github.com/ZahuRuslan/Generator-Albume-Foto.git
```
Am conectat proiectul de pe calculator la repository-ul de pe GitHub.

### 7. git push
```bash
git push -u origin main
```
Am încărcat fișierele proiectului pe GitHub.

### 8. git clone
```bash
git clone https://github.com/ZahuRuslan/Generator-Albume-Foto.git Generator-Albume-Nou
```
Am descărcat proiectul de pe GitHub într-un folder nou.

### 9. git ls-files
```bash
git ls-files
```
Am verificat ce fișiere sunt urmărite de Git.

### 10. git remote -v
```bash
git remote -v
```
Am verificat dacă repository-ul este conectat corect la GitHub.

### 11. git log
```bash
git log --oneline
```
Am verificat commit-urile făcute în proiect.

### 12. Actualizarea proiectului
```bash
git add -A
git commit -m "Actualizare proiect Hostat"
git push origin main
```
După ce am modificat proiectul, am salvat schimbările și am încărcat noua versiune pe GitHub.