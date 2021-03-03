USERNAME=${1}
TOKEN=${2}

for i in $(ls); do curl -X POST 'https://packagist.org/api/create-package?username=${USERNAME}&apiToken=${TOKEN}' -d '{"repository":{"url":"https://github.com/jellyfishphp/${i}"}}'; done;
