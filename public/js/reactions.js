function toggleReaction(postId, reactionType, event) {
    // Prevent default action (e.g., form submission or page navigation)
    if (event) {
        event.preventDefault();
    }

    // Get CSRF token from the page
    const csrfToken = document.querySelector('input[name="csrf_token_name"]')?.value;

    console.log('CSRF Token:', csrfToken);  // Log CSRF token to verify it's being fetched

    if (!csrfToken) {
        console.error('CSRF token is missing!');
        alert('CSRF token is missing. Please try again later.');
        return;
    }

    // Send request to server
    fetch(`${BASE_URL}/post/reaction`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,  // Include CSRF token in request headers
        },
        body: JSON.stringify({
            post_id: postId,
            reaction_type: reactionType,
        }),
    })
    .then(response => response.text())  // Use response.text() to check raw response
    .then(text => {
        console.log('Raw response:', text);  // Log the raw response from the server
        
        if (text.trim() === '') {
            console.error('Empty response from the server');
            alert('Server returned an empty response. Please try again later.');
            return;
        }

        try {
            const data = JSON.parse(text);  // Parse the response into JSON
            if (data.success) {
                const countElement = document.getElementById(`${reactionType}Count`);
                if (countElement) {
                    countElement.textContent = data.reactionCount;  // Update the count
                }

                // Toggle button state (active/inactive)
                const buttonId = `${reactionType}Button`;
                const buttonElement = document.getElementById(buttonId);
                if (buttonElement) {
                    buttonElement.classList.toggle('text-danger');
                }
            } else {
                console.error('Server response:', data);  // Log the server response for debugging
                alert(data.message || 'Failed to update reaction. Please try again.');
            }
        } catch (error) {
            console.error('JSON parse error:', error);  // Handle JSON parsing errors
            alert('Failed to parse server response. Please try again later.');
        }
    })
    .catch(error => {
        console.error('Network or fetch error:', error);  // Log network or fetch errors
        alert('An error occurred. Please try again later.');
    });
}