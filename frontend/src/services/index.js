import api from "./api";

export const authService = {
  login: async (email, password) => {
    const response = await api.post("/auth", { email, password });
    if (response.data.success) {
      localStorage.setItem("token", response.data.data.token);
      localStorage.setItem("user", JSON.stringify(response.data.data.user));
    }
    return response.data;
  },

  register: async (userData) => {
    const response = await api.post("/auth?action=register", userData);
    if (response.data.success) {
      localStorage.setItem("token", response.data.data.token);
      localStorage.setItem("user", JSON.stringify(response.data.data.user));
    }
    return response.data;
  },

  logout: () => {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
  },

  getCurrentUser: () => {
    const user = localStorage.getItem("user");
    return user ? JSON.parse(user) : null;
  },

  isAuthenticated: () => {
    return !!localStorage.getItem("token");
  },
};

export const productService = {
  getAll: async (filters = {}) => {
    const params = new URLSearchParams(filters).toString();
    const response = await api.get(`/products?${params}`);
    return response.data;
  },

  getById: async (id) => {
    const response = await api.get(`/products?id=${id}`);
    return response.data;
  },

  create: async (productData) => {
    const response = await api.post("/products", productData);
    return response.data;
  },

  update: async (id, productData) => {
    const response = await api.put(`/products?id=${id}`, productData);
    return response.data;
  },

  delete: async (id) => {
    const response = await api.delete(`/products?id=${id}`);
    return response.data;
  },
};

export const customerService = {
  getAll: async (filters = {}) => {
    const params = new URLSearchParams(filters).toString();
    const response = await api.get(`/customers?${params}`);
    return response.data;
  },

  getById: async (id) => {
    const response = await api.get(`/customers?id=${id}`);
    return response.data;
  },

  create: async (customerData) => {
    const response = await api.post("/customers", customerData);
    return response.data;
  },

  update: async (id, customerData) => {
    const response = await api.put(`/customers?id=${id}`, customerData);
    return response.data;
  },

  delete: async (id) => {
    const response = await api.delete(`/customers?id=${id}`);
    return response.data;
  },
};

export const saleService = {
  getAll: async (filters = {}) => {
    const params = new URLSearchParams(filters).toString();
    const response = await api.get(`/sales?${params}`);
    return response.data;
  },

  getById: async (id) => {
    const response = await api.get(`/sales?id=${id}`);
    return response.data;
  },

  create: async (saleData) => {
    const response = await api.post("/sales", saleData);
    return response.data;
  },

  updateStatus: async (id, status) => {
    const response = await api.put(`/sales?id=${id}`, { status });
    return response.data;
  },

  delete: async (id) => {
    const response = await api.delete(`/sales?id=${id}`);
    return response.data;
  },
};

export const expenseService = {
  getAll: async (filters = {}) => {
    const params = new URLSearchParams(filters).toString();
    const response = await api.get(`/expenses?${params}`);
    return response.data;
  },

  getById: async (id) => {
    const response = await api.get(`/expenses?id=${id}`);
    return response.data;
  },

  getCategories: async () => {
    const response = await api.get("/expenses?action=categories");
    return response.data;
  },

  create: async (expenseData) => {
    const response = await api.post("/expenses", expenseData);
    return response.data;
  },

  update: async (id, expenseData) => {
    const response = await api.put(`/expenses?id=${id}`, expenseData);
    return response.data;
  },

  delete: async (id) => {
    const response = await api.delete(`/expenses?id=${id}`);
    return response.data;
  },
};

export const dashboardService = {
  getStats: async (dateFrom, dateTo) => {
    const params = new URLSearchParams({
      date_from: dateFrom,
      date_to: dateTo,
    }).toString();
    const response = await api.get(`/dashboard?${params}`);
    return response.data;
  },
};
