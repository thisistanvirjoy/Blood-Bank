// Sample data for blood donations
const donationData = [
    {
        donorName: "sumit",
        disease: "Nothing",
        age: 24,
        bloodGroup: "O+",
        unit: 7,
        requestDate: "Feb. 14, 2021",
        status: "Approved",
        unitsAdded: 7
    },
    {
        donorName: "sumit",
        disease: "Nothing",
        age: 24,
        bloodGroup: "B+",
        unit: 3,
        requestDate: "Feb. 14, 2021",
        status: "Rejected",
        unitsAdded: 0
    },
    {
        donorName: "sachin",
        disease: "Nothing",
        age: 34,
        bloodGroup: "B-",
        unit: 3,
        requestDate: "Feb. 14, 2021",
        status: "Pending"
    },
    {
        donorName: "sachin",
        disease: "Nothing",
        age: 20,
        bloodGroup: "AB-",
        unit: 7,
        requestDate: "Feb. 14, 2021",
        status: "Pending"
    },
    {
        donorName: "mona",
        disease: "Nothing",
        age: 34,
        bloodGroup: "AB-",
        unit: 4,
        requestDate: "Feb. 14, 2021",
        status: "Pending"
    }
];

// Function to populate the donation table
function populateDonationTable() {
    const tableBody = document.getElementById('donationTableBody');
    tableBody.innerHTML = '';

    donationData.forEach(donation => {
        const row = document.createElement('tr');
        
        row.innerHTML = `
            <td>${donation.donorName}</td>
            <td>${donation.disease}</td>
            <td>${donation.age}</td>
            <td>${donation.bloodGroup}</td>
            <td>${donation.unit}</td>
            <td>${donation.requestDate}</td>
            <td class="status-${donation.status.toLowerCase()}">${donation.status}</td>
            <td>
                ${getActionButtons(donation)}
            </td>
        `;
        
        tableBody.appendChild(row);
    });
}

// Function to generate action buttons or status based on donation status
function getActionButtons(donation) {
    if (donation.status === 'Pending') {
        return `
            <button class="btn btn-approve" onclick="handleDonation('approve', '${donation.donorName}')">APPROVE</button>
            <button class="btn btn-reject" onclick="handleDonation('reject', '${donation.donorName}')">REJECT</button>
        `;
    } else {
        const className = donation.status === 'Approved' ? 'success' : 'danger';
        return `<span class="units-added ${className}">${donation.unitsAdded} Unit Added To Stock</span>`;
    }
}

// Function to handle donation approval/rejection
function handleDonation(action, donorName) {
    const donation = donationData.find(d => d.donorName === donorName && d.status === 'Pending');
    if (donation) {
        donation.status = action === 'approve' ? 'Approved' : 'Rejected';
        donation.unitsAdded = action === 'approve' ? donation.unit : 0;
        populateDonationTable();
    }
}

// Initialize the table when the page loads
document.addEventListener('DOMContentLoaded', () => {
    populateDonationTable();
});

// Logout functionality
document.getElementById('logoutBtn').addEventListener('click', (e) => {
    e.preventDefault();
    // Add logout logic here
    alert('Logout clicked');
});