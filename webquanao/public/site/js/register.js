// Logic chọn địa chỉ
var citis = document.getElementById("city");
var districts = document.getElementById("district");
var wards = document.getElementById("ward");
var Parameter = {
	url: "http://localhost:8080/api/read-json",
	method: "GET",
	responseType: "application/json",
};
var promise = axios(Parameter);
promise.then(function (result) {
	renderCity(result.data);
});

function renderCity(data) {
	for (const x of data) {
		citis.options[citis.options.length] = new Option(x.Name, x.Id);
	}
	citis.onchange = function () {
		district.length = 1;
		ward.length = 1;
		if (this.value != "") {
			const result = data.filter((n) => n.Id === this.value);

			for (const k of result[0].Districts) {
				district.options[district.options.length] = new Option(k.Name, k.Id);
			}
		}
	};
	district.onchange = function () {
		ward.length = 1;
		const dataCity = data.filter((n) => n.Id === citis.value);
		if (this.value != "") {
			const dataWards = dataCity[0].Districts.filter(
				(n) => n.Id === this.value
			)[0].Wards;

			for (const w of dataWards) {
				wards.options[wards.options.length] = new Option(w.Name, w.Id);
			}
		}
	};
}
let recaptchaToken = "";
const hiddenDataEl = document.getElementById("hiddenData");
const recaptchaEnabled =
	hiddenDataEl && hiddenDataEl.getAttribute("data-recaptcha-enabled") === "1";

function onCaptchaSuccess(token) {
	recaptchaToken = token;
	document.getElementById("submitBtn").disabled = false;
}

function validateRecaptcha() {
	if (!recaptchaEnabled) {
		return true;
	}
	if (!recaptchaToken) {
		// Loại thông báo: 'success' hoặc 'error'
		const type = "error"; // hoặc 'error'

		Swal.fire({
			toast: true,
			position: "top",
			icon: type,
			title:
				type === "success"
					? "Thành công! Dữ liệu đã được lưu."
					: "Vui lòng xác nhận reCAPTCHA trước khi gửi.",
			showConfirmButton: false,
			showCloseButton: true,
			timer: 2500,
			timerProgressBar: true,
			customClass: {
				popup: `custom-toast ${
					type === "success" ? "swal2-success-toast" : "swal2-error-toast"
				}`,
			},
			didOpen: (toast) => {
				toast.addEventListener("mouseenter", Swal.stopTimer);
				toast.addEventListener("mouseleave", Swal.resumeTimer);
			},
		});
		return false;
	}
	return true;
}

let isFetching = false;
document
	.getElementById("submitBtn")
	.addEventListener("click", async function (event) {
		if (isFetching) {
			// Loại thông báo: 'success' hoặc 'error'
			const type = "error"; // hoặc 'error'

			Swal.fire({
				toast: true,
				position: "top",
				icon: type,
				title: "Yêu cầu đang được thực hiện, vui lòng đợi...",
				showConfirmButton: false,
				showCloseButton: true,
				timer: 2500,
				timerProgressBar: true,
				customClass: {
					popup: `custom-toast ${
						type === "success" ? "swal2-success-toast" : "swal2-error-toast"
					}`,
				},
				didOpen: (toast) => {
					toast.addEventListener("mouseenter", Swal.stopTimer);
					toast.addEventListener("mouseleave", Swal.resumeTimer);
				},
			});
			return; // Nếu đang fetch, không làm gì cả
		}
		isFetching = true;
		let isValid = true;

		const response = recaptchaEnabled && typeof grecaptcha !== "undefined"
			? grecaptcha.getResponse()
			: "dev-skip";

		function showError(input, message) {
			clearError(input);
			let errorMsg = document.createElement("p");
			errorMsg.className = "text-red-500 text-xl mt-1";
			errorMsg.innerText = message;
			input.classList.add("border-red-500");
			input.parentNode.appendChild(errorMsg);
		}

		function clearError(input) {
			input.classList.remove("border-red-500");
			let errorMsg = input.parentNode.querySelector(".text-red-500");
			if (errorMsg) {
				errorMsg.remove();
			}
		}

		// Kiểm tra Họ và Tên
		let nameInput = document.getElementById("name");
		if (nameInput.value.trim() === "") {
			showError(nameInput, "Họ và tên không được để trống");
			isValid = false;
		} else {
			clearError(nameInput);
		}

		// Kiểm tra Email
		let emailInput = document.getElementById("email");
		let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		if (!emailRegex.test(emailInput.value.trim())) {
			showError(emailInput, "Email không hợp lệ");
			isValid = false;
		} else {
			clearError(emailInput);
		}

		// Kiểm tra mật khẩu
		let passwordInput = document.getElementById("password");
		let re_passwordInput = document.getElementById("re-password");
		let passwordValue = passwordInput.value.trim();
		if (passwordValue.length < 8) {
			showError(passwordInput, "Password phải từ 8 kí tự trở lên");
			isValid = false;
		} else {
			clearError(passwordInput);
		}

		if (passwordInput.value !== re_passwordInput.value) {
			showError(re_passwordInput, "Mật khẩu nhập lại không khớp");
			isValid = false;
		} else {
			clearError(re_passwordInput);
		}

		// Kiểm tra Số điện thoại
		let phoneInput = document.getElementById("phone");
		let phoneRegex = /^[0-9]{8,11}$/;
		if (!phoneRegex.test(phoneInput.value.trim())) {
			showError(phoneInput, "Số điện thoại không hợp lệ (8-11 chữ số)");
			isValid = false;
		} else {
			clearError(phoneInput);
		}

		// Kiểm tra Địa chỉ
		let addressInput = document.getElementById("address");
		if (addressInput.value.trim() === "") {
			showError(addressInput, "Địa chỉ không được để trống");
			isValid = false;
		} else {
			clearError(addressInput);
		}

		// Kiểm tra Tỉnh/Thành
		let citySelect = document.getElementById("city");
		if (citySelect.value === "") {
			showError(citySelect, "Vui lòng chọn Tỉnh/Thành");
			isValid = false;
		} else {
			clearError(citySelect);
		}

		// Kiểm tra Quận/Huyện
		let districtSelect = document.getElementById("district");
		if (districtSelect.value === "") {
			showError(districtSelect, "Vui lòng chọn Quận/Huyện");
			isValid = false;
		} else {
			clearError(districtSelect);
		}

		// Kiểm tra Phường/Xã
		let wardSelect = document.getElementById("ward");
		if (wardSelect.value === "") {
			showError(wardSelect, "Vui lòng chọn Phường/Xã");
			isValid = false;
		} else {
			clearError(wardSelect);
		}

		// Nếu có lỗi, không gửi API
		if (!isValid){
			isFetching = false;
			return;
		}

		// Kiểm tra recaptcha
		if (!validateRecaptcha() || (recaptchaEnabled && !response)) {
			isFetching = false;
			return;
		}

		// Tạo object chứa dữ liệu cần gửi
		email = emailInput.value.trim();

		let formData = {
			name: nameInput.value.trim(),
			email: email,
			password: passwordInput.value.trim(),
			phone: phoneInput.value.trim(),
			address: addressInput.value.trim(),
			city: citySelect.options[citySelect.selectedIndex].text,
			district: districtSelect.options[districtSelect.selectedIndex].text,
			ward: wardSelect.options[wardSelect.selectedIndex].text,
			recaptcha: recaptchaToken,
		};

		const registerUrl =
			(window.location.origin || "") + "/user/register";

		try {
			const response = await fetch(registerUrl, {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify(formData),
			});
			const text = await response.text();
			const data = JSON.parse(text);

			if (data.status === "success") {
				const redirectLogin = () => {
					window.location.href = "/dang-nhap";
				};
				await Swal.fire({
					icon: "success",
					title: "Đăng ký thành công",
					text: data.message,
					confirmButtonText: "Đăng nhập ngay",
					customClass: { confirmButton: "my-custom-button" },
				});
				redirectLogin();
			} else if (data.status === "error") {
				let detailHTML = "";
				if (data.errors) {
					detailHTML += '<ul style="text-align: left;">';
					for (const key in data.errors) {
						detailHTML += `<li><strong>${key}:</strong> ${data.errors[key]}</li>`;
					}
					detailHTML += "</ul>";
				}
				await Swal.fire({
					icon: "error",
					title: "Đăng ký thất bại",
					text: data.message || "Vui lòng kiểm tra lại thông tin.",
					html: detailHTML || undefined,
					confirmButtonText: "Thử lại",
					customClass: { confirmButton: "my-custom-button" },
				});
				if (recaptchaEnabled && typeof grecaptcha !== "undefined") {
					grecaptcha.reset();
				}
				recaptchaToken = "";
			}
		} catch (error) {
			console.error(error);
			await Swal.fire({
				icon: "error",
				title: "Lỗi!",
				text: "Đã có lỗi xảy ra, vui lòng thử lại.",
				customClass: { confirmButton: "my-custom-button" },
			});
			if (recaptchaEnabled && typeof grecaptcha !== "undefined") {
				grecaptcha.reset();
			}
			recaptchaToken = "";
		}
		isFetching = false;
	});
