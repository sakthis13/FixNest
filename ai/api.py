import sys
import json

from ai_pipeline import analyze_complaint


def main():

    # --------------------------------------------------------
    # CHECK INPUT
    # --------------------------------------------------------

    if len(sys.argv) < 3:

        print(json.dumps({
            "error": "Complaint and category are required"
        }))

        return


    # --------------------------------------------------------
    # GET INPUT FROM PHP
    # --------------------------------------------------------

    complaint = sys.argv[1]
    category = sys.argv[2]


    # --------------------------------------------------------
    # RUN AI ANALYSIS
    # --------------------------------------------------------

    try:

        result = analyze_complaint(
            complaint,
            category
        )

        print(
            json.dumps(
                result,
                ensure_ascii=False
            )
        )


    except Exception as e:

        print(
            json.dumps({
                "error": str(e)
            })
        )


if __name__ == "__main__":

    main()