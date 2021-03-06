const dragstart_handler = event => {
    // Add the target element's id to the data transfer object
    event.dataTransfer.setData("text/plain", event.target.id);
    event.dropEffect = "move";
};

const dragover_handler = event => {
    event.preventDefault();
    // Set the dropEffect to move
    event.dataTransfer.dropEffect = "move";
};

const drop_handler = event => {
    event.preventDefault();
    // Get the id of the target and add the moved element to the target's DOM
    var card = document.getElementById(event.dataTransfer.getData("text"));
    event.target.appendChild(card);
    fetch('/' + ((card.getAttribute('data-type') === 'bug') ? 'bugs' : 'evolutions') + '/' + card.getAttribute('data-id'), {
        method: 'PUT', 
        body: JSON.stringify({
            status: event.target.getAttribute('data-status')
        }),
        credentials: 'include'
    }).then(response => {
        console.log(response);
    });
};

const create_comment = (id, type) => {
    var textArea = document.querySelector('textarea[name="comment-content"]');
    var content = textArea.value;
    if (content.length === 0) return false;
    
    fetch(`/feedbacks/${id}/commentaries`, {
        method: 'POST', 
        body: JSON.stringify({
            type: type,
            content: content
        }),
        credentials: 'include'
    }).then(response => {
        textArea.value = '';
        if (response.ok) {
            return response.json();
        }
        throw 'Erreur';
    }).then(data => {
        console.log(data);
        
        var commentsBox = document.querySelector('.comments');
        var comment = document.createElement('div');
        comment.classList.add('comment'); 
        
        var contentElement = document.createElement('div');
        contentElement.classList.add('content');
        contentElement.innerHTML = data.feedback.content;
        comment.appendChild(contentElement);
        
        var authorElement = document.createElement('div');
        authorElement.classList.add('author');
        authorElement.innerHTML = `${data.feedback.author}, ${data.created_at_string}`;
        comment.appendChild(authorElement);
        
        commentsBox.appendChild(comment);
    }).catch(error => console.log(error));
};