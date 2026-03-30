import React, { useState, useEffect } from "react";
import { saleService, productService, customerService } from "../services";

function Sales() {
  const [sales, setSales] = useState([]);
  const [products, setProducts] = useState([]);
  const [customers, setCustomers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [formData, setFormData] = useState({
    customer_id: "",
    sale_date: new Date().toISOString().split("T")[0],
    payment_method: "cash",
    discount: 0,
    tax: 0,
    notes: "",
    items: [
      {
        product_id: "",
        product_name: "",
        quantity: 1,
        unit_price: 0,
        subtotal: 0,
      },
    ],
  });

  useEffect(() => {
    loadSales();
    loadProducts();
    loadCustomers();
  }, []);

  const loadSales = async () => {
    try {
      const response = await saleService.getAll();
      setSales(response.data);
    } catch (error) {
      console.error("Failed to load sales:", error);
    } finally {
      setLoading(false);
    }
  };

  const loadProducts = async () => {
    try {
      const response = await productService.getAll();
      setProducts(response.data);
    } catch (error) {
      console.error("Failed to load products:", error);
    }
  };

  const loadCustomers = async () => {
    try {
      const response = await customerService.getAll();
      setCustomers(response.data);
    } catch (error) {
      console.error("Failed to load customers:", error);
    }
  };

  const handleAdd = () => {
    setFormData({
      customer_id: "",
      sale_date: new Date().toISOString().split("T")[0],
      payment_method: "cash",
      discount: 0,
      tax: 0,
      notes: "",
      items: [
        {
          product_id: "",
          product_name: "",
          quantity: 1,
          unit_price: 0,
          subtotal: 0,
        },
      ],
    });
    setShowModal(true);
  };

  const handleDelete = async (id) => {
    if (window.confirm("Are you sure you want to delete this sale?")) {
      try {
        await saleService.delete(id);
        loadSales();
      } catch (error) {
        alert("Failed to delete sale");
      }
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const total = calculateTotal();
      await saleService.create({
        ...formData,
        total_amount: total,
        status: "completed",
      });
      setShowModal(false);
      loadSales();
    } catch (error) {
      alert("Failed to create sale");
    }
  };

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  const handleProductChange = (index, field, value) => {
    const newItems = [...formData.items];
    newItems[index][field] = value;

    if (field === "product_id" && value) {
      const product = products.find((p) => p.id === parseInt(value));
      if (product) {
        newItems[index].product_name = product.name;
        newItems[index].unit_price = parseFloat(product.price);
      }
    }

    if (field === "quantity" || field === "unit_price") {
      newItems[index].subtotal =
        newItems[index].quantity * newItems[index].unit_price;
    }

    setFormData({ ...formData, items: newItems });
  };

  const addItem = () => {
    setFormData({
      ...formData,
      items: [
        ...formData.items,
        {
          product_id: "",
          product_name: "",
          quantity: 1,
          unit_price: 0,
          subtotal: 0,
        },
      ],
    });
  };

  const removeItem = (index) => {
    const newItems = formData.items.filter((_, i) => i !== index);
    setFormData({ ...formData, items: newItems });
  };

  const calculateTotal = () => {
    const subtotal = formData.items.reduce(
      (sum, item) => sum + parseFloat(item.subtotal || 0),
      0,
    );
    return (
      subtotal -
      parseFloat(formData.discount || 0) +
      parseFloat(formData.tax || 0)
    );
  };

  if (loading) return <div className="loading">Loading...</div>;

  return (
    <div>
      <div className="table-container">
        <div className="table-header">
          <h2>Sales</h2>
          <button className="btn btn-success" onClick={handleAdd}>
            New Sale
          </button>
        </div>

        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Customer</th>
              <th>Total</th>
              <th>Payment</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {sales.map((sale) => (
              <tr key={sale.id}>
                <td>{sale.sale_date}</td>
                <td>{sale.customer_name || "Walk-in"}</td>
                <td>${parseFloat(sale.total_amount).toFixed(2)}</td>
                <td>{sale.payment_method}</td>
                <td>{sale.status}</td>
                <td className="actions">
                  <button
                    className="btn btn-danger"
                    onClick={() => handleDelete(sale.id)}
                  >
                    Delete
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {showModal && (
        <div className="modal-overlay">
          <div className="modal">
            <div className="modal-header">
              <h3>New Sale</h3>
              <button className="close" onClick={() => setShowModal(false)}>
                &times;
              </button>
            </div>

            <form onSubmit={handleSubmit}>
              <div className="form-group">
                <label>Customer</label>
                <select
                  name="customer_id"
                  value={formData.customer_id}
                  onChange={handleChange}
                >
                  <option value="">Walk-in Customer</option>
                  {customers.map((customer) => (
                    <option key={customer.id} value={customer.id}>
                      {customer.name}
                    </option>
                  ))}
                </select>
              </div>

              <div className="form-group">
                <label>Sale Date</label>
                <input
                  type="date"
                  name="sale_date"
                  value={formData.sale_date}
                  onChange={handleChange}
                  required
                />
              </div>

              <div className="form-group">
                <label>Payment Method</label>
                <select
                  name="payment_method"
                  value={formData.payment_method}
                  onChange={handleChange}
                >
                  <option value="cash">Cash</option>
                  <option value="card">Card</option>
                  <option value="online">Online</option>
                  <option value="other">Other</option>
                </select>
              </div>

              <h4>Items</h4>
              {formData.items.map((item, index) => (
                <div
                  key={index}
                  style={{
                    border: "1px solid #ddd",
                    padding: "10px",
                    marginBottom: "10px",
                  }}
                >
                  <div className="form-group">
                    <label>Product</label>
                    <select
                      value={item.product_id}
                      onChange={(e) =>
                        handleProductChange(index, "product_id", e.target.value)
                      }
                      required
                    >
                      <option value="">Select Product</option>
                      {products.map((product) => (
                        <option key={product.id} value={product.id}>
                          {product.name} - ${product.price}
                        </option>
                      ))}
                    </select>
                  </div>

                  <div className="form-group">
                    <label>Quantity</label>
                    <input
                      type="number"
                      value={item.quantity}
                      onChange={(e) =>
                        handleProductChange(index, "quantity", e.target.value)
                      }
                      min="1"
                      required
                    />
                  </div>

                  <div className="form-group">
                    <label>Unit Price</label>
                    <input
                      type="number"
                      step="0.01"
                      value={item.unit_price}
                      onChange={(e) =>
                        handleProductChange(index, "unit_price", e.target.value)
                      }
                      disabled
                    />
                  </div>

                  <div className="form-group">
                    <label>Subtotal: ${item.subtotal.toFixed(2)}</label>
                  </div>

                  {formData.items.length > 1 && (
                    <button
                      type="button"
                      className="btn btn-danger"
                      onClick={() => removeItem(index)}
                    >
                      Remove
                    </button>
                  )}
                </div>
              ))}

              <button
                type="button"
                className="btn btn-secondary mb-20"
                onClick={addItem}
              >
                Add Item
              </button>

              <div className="form-group">
                <label>Discount</label>
                <input
                  type="number"
                  step="0.01"
                  name="discount"
                  value={formData.discount}
                  onChange={handleChange}
                />
              </div>

              <div className="form-group">
                <label>Tax</label>
                <input
                  type="number"
                  step="0.01"
                  name="tax"
                  value={formData.tax}
                  onChange={handleChange}
                />
              </div>

              <div className="form-group">
                <label>Notes</label>
                <textarea
                  name="notes"
                  value={formData.notes}
                  onChange={handleChange}
                />
              </div>

              <h3>Total: ${calculateTotal().toFixed(2)}</h3>

              <div className="modal-actions">
                <button
                  type="button"
                  className="btn btn-secondary"
                  onClick={() => setShowModal(false)}
                >
                  Cancel
                </button>
                <button type="submit" className="btn btn-success">
                  Create Sale
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}

export default Sales;
