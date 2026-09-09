import joblib

# Load trained model
model = joblib.load("severity_model.pkl")
vectorizer = joblib.load("severity_vectorizer.pkl")


def predict_severity(complaint):
    vector = vectorizer.transform([complaint])

    prediction = model.predict(vector)[0]

    probabilities = model.predict_proba(vector)[0]
    confidence = max(probabilities)

    return prediction, confidence


# Test complaints
test_complaints = [
    "The socket in my room is sparking",
    "The fan is a little slow",
    "A rat entered my room",
    "Water is leaking from the bathroom",
    "My room has not been cleaned"
]


for complaint in test_complaints:
    severity, confidence = predict_severity(complaint)

    print("\nComplaint:", complaint)
    print("Predicted severity:", severity)
    print("Confidence:", round(confidence, 3))