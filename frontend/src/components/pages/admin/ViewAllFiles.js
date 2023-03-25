import { useState, useEffect } from 'react';
import { BASE_API_URL } from '../../Api.js';
import Moment from 'react-moment';

/**
 * Admin View All Files Page
 * 
 * This page is responsible for viewing all files on the platform
 * 
 * @author Daniel Rachfal
 */
function FileVisibility(props) {
    const [selectedOption, setSelectedOption] = useState(props.visibility);
    const [currentVisibility, setCurrentVisibility] = useState(props.visibility);

    const visibilityMap = {
        'PUBLIC': 1,
        'PRIVATE': 2
    };

    useEffect(() => {
        if (selectedOption !== currentVisibility) {
            const bodyString = "file_id=" + props.file_id + "&visibility=" + visibilityMap[selectedOption]
            fetch(BASE_API_URL + "/updateFileVisibility", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: bodyString
            })
                .then((response) => response.json())
                .then((data) => {
                    props.onSuccess("Visibility changed");
                })
            }
    }, [selectedOption]);

    const options = [
        { label: "Public", value: "PUBLIC" },
        { label: "Private", value: "PRIVATE" },
    ];

    const handleSelectChange = (event) => {
        setSelectedOption(event.target.value);
    }

    return (
        <div>
          <select
            id="visibility-select"
            value={selectedOption}
            onChange={handleSelectChange}
          >
            {options.map((option) => (
              <option key={option.value} value={option.value}>
                {option.label}
              </option>
            ))}
          </select>
        </div>
      );
}

function ViewAllFiles() {
    const [files, setFiles] = useState([]);
    const [errorMessage, setErrorMessage] = useState("");
    const [successMessage, setSuccessMessage] = useState(""); 
    const handleSuccessMessage = (message) => {
        setSuccessMessage(message);
    }

    // Fetch the files from the API
    useEffect(() => {
        fetch (BASE_API_URL + "/files")
            .then((res) => res.json())
            .then((response) => {
                setFiles(response['data']);
            })
            .catch((error) => {
                setErrorMessage("Something has went wrong when fetching the files. Please contact your administrator for help");
            })
    }, []);

    return (
        <div className="container">
            <div className="card card-primary">
                <div className="card-header">
                    View All Files
                </div>
                <div className="card-body">
                    {/* Only render success message and error message if they're not null */}
                    {successMessage !== "" ? 
                    <div className="bg-success rounded">
                        <p className="p-2 fw-bold text-light">{successMessage}</p>
                    </div> : null}
                    {errorMessage !== "" ? 
                    <div className="bg-danger rounded">
                        <p className="p-2 fw-bold">{errorMessage}</p> 
                    </div> : null}
                    <table className="table table-striped">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Visibility</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {files.map((file) => (
                                <tr key={file.id}>
                                    <td>{file.fileName}</td>
                                    {/* Only capitalizes the first letter of the string */}
                                    <td style={{textTransform: 'capitalize'}}>
                                        <FileVisibility
                                            file_id={file.id}
                                            visibility={file.visibility}
                                            onSuccess={handleSuccessMessage}
                                        />
                                    </td>
                                    <td>{file.createdByEmail}</td>
                                    <td><Moment unix format="DD/MM/YYYY hh:mm">{file.createdAt}</Moment></td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}

export default ViewAllFiles;