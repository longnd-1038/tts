<!DOCTYPE html>
<html>
<head>
    <title>TTS Demo</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>TTS</h1>
    <textarea id="textToSpeech" maxlength="500" rows="5" cols="33"></textarea>
    <br>
    <button id="submitBtn">Submit</button>


    <h1>TTS TXT</h1>
    <form id="uploadForm" enctype="multipart/form-data">
    @csrf <!-- CSRF token for security -->
        <input type="file" id="textFile" accept=".txt">
        <br><br>
        <button type="button" id="convertBtn">Convert to Speech</button>
    </form>


    <div id="result"></div>

    <script>
        $(document).ready(function() {
            $('#submitBtn').click(function() {
                var text = $('#textToSpeech').val();
                if (text.trim() === '') {
                    alert('Please enter some text.');
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: '/api/ttsfree', // Route for submitting text to the API
                    data: {
                        _token: '{{ csrf_token() }}',
                        text: text
                    },
                    success: function(data) {
                        // Display the audio result
                        $('#result').html('<audio controls><source src="data:audio/mpeg;base64,' + data.file_mp3 + '" type="audio/mpeg"></audio>');
                    },
                    error: function(error) {
                        alert('Failed to generate TTS audio.');
                    }
                });
            });

            $('#convertBtn').click(function () {
                var fileInput = $('#textFile')[0].files[0];
                if (!fileInput) {
                    alert('Please select a .txt file.');
                    return;
                }

                var formData = new FormData();
                formData.append('text_file', fileInput);

                $.ajax({
                    url: '/api/ttsfree',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $('#result').html('<audio controls><source src="data:audio/mpeg;base64,' + response.file_mp3 + '" type="audio/mpeg"></audio>');
                    },
                    error: function (error) {
                        $('#result').html('Error: ' + error.responseText);
                    }
                });
            });
        });
    </script>
</body>
</html>
