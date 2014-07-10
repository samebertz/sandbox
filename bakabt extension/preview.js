console.log('bakabt extension preview.js loaded');

var categories = ['1', '2', '5'];

var preview = document.body.appendChild(document.createElement("div"));
preview.setAttribute('id', 'test');

function more() {
    var requestDocument = this.responseXML;
    //console.log(requestDocument);
    var cover = requestDocument.getElementsByTagName('img');
    if(cover !== null) cover = cover[0];
    console.log(cover);
    preview.style.backgroundImage = 'url(\'' + cover.getAttribute('src') + '\')';
}

function stuff(e) {
    e = e || window.event;
    var target = e.target;
    if (target.tagName.toLowerCase() === 'a' && target.className.toLowerCase() === 'title') {
        var cat = target.parentNode.parentNode.previousSibling.previousSibling.firstChild.nextSibling.firstChild.nextSibling.getAttribute('href');
        if(categories.indexOf(cat.charAt(cat.length - 1)) !== -1) {
            var rect = target.getBoundingClientRect();
            preview.style.height = rect.height + 'px';
            preview.style.width = rect.width/2 + 'px';
            preview.style.top = rect.top + document.body.scrollTop + 'px';
            preview.style.left = rect.left + document.body.scrollLeft + 'px';
            var request = new XMLHttpRequest();
            request.onload = more;
            request.open('GET', 'http://www.bakabt.me/description.php?id=' + target.getAttribute('href').substring(1,7));
            request.responseType = 'document';
            request.send();
        }
    }
}

var table = document.getElementsByClassName('torrents')[0];
table.addEventListener('mouseover', stuff, false);